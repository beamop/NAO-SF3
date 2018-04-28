<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Observation;
use AppBundle\Entity\User;
use AppBundle\Form\MailerType;
use AppBundle\Service\Mailer;
use AppBundle\Entity\Post;
use AppBundle\Form\ObservationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Welp\MailchimpBundle\Event\SubscriberEvent;
use Welp\MailchimpBundle\Subscriber\Subscriber;
use AppBundle\Event\NaoEvents;
use AppBundle\Event\PostObservationEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NaoController extends Controller
{
    private $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/", name="nao_accueil")
     */
    public function indexAction(Request $request)
    {
        /**
         * Connexion depuis l'index
         *
         * @var $session Session
         */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;



        $observation = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findallValidatedBirds();

        $articles = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(
                array('status' => array(Post::PUBLISHED, Post::FEATURED)),
                array('status' => 'DESC', 'publishedAt' => 'DESC'),
                10,
                0
            );

        return $this->render('nao/index.html.twig', array(
            'observations' => $observation,
            'articles' => $articles,
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    public function getTokenAction()
    {
        return new Response($this->get('security.csrf.token_manager')->getToken('authenticate')->getValue());
    }

    /**
     * @Route("/observation", name="nao_observation_carte")
     */
    public function carteObservationAction()
    {
        return $this->redirectToRoute('nao_liste_observation');
    }

    /**
     * @Route("/observation/detail/{id}", name="nao_details_observation")
     */
    public function detailsObservationAction($id)
    {
        $observation = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->find($id);

        return $this->render('nao/observation/details.html.twig', array(
            'observation' => $observation
        ));
    }

    /**
     * @Route("/observation/ajouter", name="nao_ajouter_observation")
     */
    public function ajouterObservationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $observation = new Observation();

        $form = $this->createForm(ObservationType::class, $observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile $image
             */
            $image = $observation->getImage();

            if ($image instanceof UploadedFile) {
                $nom_image = $this->generateUniqueFilename().'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $nom_image
                );

                $observation->setImage($nom_image);
            } else {
                $observation->setImage('nao_observation_default.jpeg');
            }

            $utilisateur = $this->get('security.token_storage')->getToken()->getUser();
            $observation->setUser($utilisateur);

            if ($this->get('security.authorization_checker')->isGranted('ROLE_NATURALISTE')) {
                $observation->setValidation(Observation::VALIDATED);
            } else {
                $observation->setValidation(Observation::WAITING);
            }

            $em->persist($observation);
            $em->flush();

            if ($observation->getValidation() === Observation::WAITING) {
                // On récupère les mails des naturalistes
                $bccNaturalistes = null;
                $naturalistes = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->findNaturalistes();
                foreach ($naturalistes as $naturaliste) {
                    $bccNaturalistes[$naturaliste->getEmail()] = $naturaliste->getUsername();
                }

                // On crée l'événement
                $event = new PostObservationEvent($observation, $bccNaturalistes);

                // On déclenche l'évènement
                $this->get('event_dispatcher')->dispatch(NaoEvents::POST_OBSERVATION, $event);
            }


            return $this->redirectToRoute('nao_observation_carte');
        }

        return $this->render('nao/observation/ajouter.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("/observation/liste/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="nao_liste_observation")
     */
    public function listeObservationAction($page)
    {
        $maxObsParPage = $this->container->getParameter('max_obs_par_page');

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllValidatedBirds($page, $maxObsParPage);

        //dump(count($observations));

        $pagination = array(
            'page' => $page,
            'route' => 'nao_liste_observation',
            'pages_count' => ceil(count($observations) / $maxObsParPage),
            'route_params' => array()
        );

        return $this->render('nao/observation/liste.html.twig', array(
            'observations' => $observations,
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/observation/search/bird", name="nao_observation_json_bird", methods="POST")
     */
    public function observationSearchBirdAction(Request $request)
    {
        $birdId = (int) $request->request->get('bird');
        $result = array();
        $nb = 0;

        if (!empty($birdId)) {

            $observations = $this->getDoctrine()
                ->getRepository(Observation::class)
                ->byBirdId($birdId);

        } else {
            $observations = $this->getDoctrine()
                ->getRepository(Observation::class)
                ->findAllValidatedBirds();
        }

        foreach ($observations as $obs){
            $result[] = array(
                'id'    => $obs->getId(),
                'userName' => $obs->getUser()->getUsername(),
                'birdName'  => $obs->getBird()->getNomCourant(),
                'dateObservation' => $obs->getDate()->format('d/m/Y'),
                'latitude' => $obs->getLatitude(),
                'longitude' => $obs->getLongitude(),
            );
            $nb++;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/gestion/en-attente/observation", name="nao_validation_observation")
     */
    public function validationObservationAction()
    {
        $observationsOnHold = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllNoValidatedBirds();

        $commentsOnHold = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAllNoValidatedComments();

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAll();

        return $this->render('naouser/admin/validation-observation.html.twig', array(
            'observationsOnHold' => $observationsOnHold,
            'commentsOnHold' => $commentsOnHold,
            'observations' => $observations
        ));
    }

    /**
     * @Route("/gestion/en-attente/comment", name="nao_validation_comment")
     */
    public function validationCommentAction()
    {
        $observationsOnHold = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllNoValidatedBirds();

        $commentsOnHold = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAllNoValidatedComments();

        return $this->render('naouser/admin/validation-comment.html.twig', array(
            'observationsOnHold' => $observationsOnHold,
            'commentsOnHold' => $commentsOnHold
        ));
    }

    /**
     * @Route("/gestion/en-attente/valider/{id}", name="nao_validation_observation_valider", requirements={"id": "\d+"})
     * Method({"GET", "POST"})
     */
    public function validerAction(Request $request, $id)
    {
        $observation = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->find($id);

       $observation->setValidation(Observation::VALIDATED);

       $entityManager = $this->getDoctrine()->getManager();

       $entityManager->flush($observation);

       return $this->redirectToRoute('nao_validation_observation');
    }

    /**
     * @Route("/gestion/en-attente/supprimer/{id}", name="nao_validation_observation_supprimer", requirements={"id": "\d+"})
     * Method({"GET", "POST"})
     */
    public function supprimerAction(Request $request, $id)
    {
        $observation = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($observation);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('nao_admin');
    }

    /**
     * @Route("/gestion/observation/liste", name="nao_liste_gestion")
     */
    public function listeGestionAction()
    {
        $observationsOnHold = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllNoValidatedBirds();

        $commentsOnHold = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAllNoValidatedComments();

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllValidatedBirds();

        return $this->render('naouser/admin/liste.html.twig', array(
            'observationsOnHold' => $observationsOnHold,
            'commentsOnHold' => $commentsOnHold,
            'observations' => $observations
        ));
    }

    /**
     * @Route("/gestion/utilisateur", name="nao_utilisateur_gestion")
     */
    public function utilisateurGestionAction()
    {
        $observationsOnHold = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllNoValidatedBirds();

        $commentsOnHold = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAllNoValidatedComments();

        $utilisateurs = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('naouser/admin/utilisateur/liste.html.twig', array(
            'observationsOnHold' => $observationsOnHold,
            'commentsOnHold' => $commentsOnHold,
            'utilisateurs' => $utilisateurs
        ));
    }

    /**
     * @Route("/gestion/utilisateur/{id}/promote", name="nao_utilisateur_promote")
     */
    public function promoteUtilisateurAction($id)
    {
        $utilisateur = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $userManager = $this->get('fos_user.user_manager');
        $utilisateur->addRole('ROLE_NATURALISTE');
        $userManager->updateUser($utilisateur);

        $this->addFlash("success", "L'utilisateur a bien été mis à jour.");

        return $this->redirectToRoute('nao_utilisateur_gestion');
    }

    /**
     * @Route("/contact", name="nao_contact")
     */
    public function contactAction(Request $request, Mailer $mailer)
    {
        $form = $this->createForm(MailerType::class,null);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($mailer->sendEmail($form->getData())) {
                $this->addFlash("success", "Votre message a été envoyé avec succès.");
            } else {
                $this->addFlash("error", "Il y a une erreur dans votre formulaire, merci de réessayer.");
            }
        }

        return $this->render('nao/contact/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/mentions-legales", name="nao_mentions_legales")
     */
    public function mentionsLegalesAction()
    {
        return $this->render('nao/mentions-legales/mentions-legales.html.twig');
    }

    /**
     * @Route("/a-propos", name="nao_apropos")
     */
    public function aProposAction()
    {
        return $this->render('nao/a-propos/a-propos.html.twig');
    }


    /**
     * @Route("/newsletter/subscribe", name="nao_newsletter_subscribe", requirements={"email"}, methods="POST")
     */
    public function subscribeNewsletterAction(Request $request, ValidatorInterface $validator)
    {

        $email = $request->request->get('email');
        $submittedToken = $request->request->get('token');

        $return = "NOK";

        /*
        // cas user connecté

        $user = $this->getUser();

        $subscriber = new Subscriber($user->getEmail(), [
            'FNAME' => $user->getUsername(),
        ], [
            'language' => 'fr'
        ]);
        */


        $emailConstraint = new Assert\Email();

        // validate
        $errorList = $validator->validate(
            $email,
            $emailConstraint
        );

        if (count($errorList) > 0) {
            return new JsonResponse($return);
        }

        if (!$this->isCsrfTokenValid('newsletter', $submittedToken)) {
            return new JsonResponse($return);
        }

        $subscriber = new Subscriber($email);

        $this->container->get('event_dispatcher')->dispatch(
            SubscriberEvent::EVENT_SUBSCRIBE,
            new SubscriberEvent('ee60b9420e', $subscriber)
        );


        return new JsonResponse('OK');
    }

}

