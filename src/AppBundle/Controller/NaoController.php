<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\MailerType;
use AppBundle\Service\Mailer;
use AppBundle\Entity\Bird;
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
use FOS\UserBundle\Event\FormEvent;

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
     * @Route("/observation/liste", name="nao_liste_observation")
     */
    public function listeObservationAction()
    {
        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllValidatedBirds();

        return $this->render('nao/observation/liste.html.twig', array(
            'observations' => $observations
        ));
    }

    /**
     * @Route("/observation/validation", name="nao_validation_observation")
     */
    public function ValidationObservationAction()
    {
        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAll();

        return $this->render('nao/observation/validation.html.twig', array(
            'observations' => $observations
        ));
    }

    /**
     * @Route("/observation/validation/valider/{id}", name="nao_validation_observation_valider", requirements={"id": "\d+"})
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
     * @Route("/observation/validation/supprimer/{id}", name="nao_validation_observation_supprimer", requirements={"id": "\d+"})
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

        return $this->redirectToRoute('nao_liste_observation');
    }

    /**
     * @Route("/observation", name="nao_observation_carte")
     */
    public function carteObservationAction()
    {
        return $this->redirectToRoute('nao_liste_observation');
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
     * @Route("/a-propos", name="nao_apropos")
     */
    public function aproposAction()
    {
        return $this->render('nao/a-propos/a-propos.html.twig');
    }

}

