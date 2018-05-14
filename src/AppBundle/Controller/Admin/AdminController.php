<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Observation;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends Controller
{
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
     * @Security("has_role('ROLE_ADMIN')")
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

       $datas['sujet'] = "Observation validée";
       $nom = $observation->getUser()->getUsername();
       $datas['contenu'] = "Bonjour " . $nom . ",<br><br>Votre observation :<br>" . $observation->getCommentaire() . "<br>a été validée !";

       $notificator = $this->container->get('nao.notificator');
       $notificator->notifyByEmail($datas);

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

        $datas['sujet'] = "Observation rejetée";
        $nom = $observation->getUser()->getUsername();
        $datas['contenu'] = "Bonjour " . $nom . ",<br><br>Nous sommes navrés mais votre observation :<br>" . $observation->getCommentaire() . "<br>a été rejetée.";

        $notificator = $this->container->get('nao.notificator');
        $notificator->notifyByEmail($datas);

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('nao_validation_observation');
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
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Route("/gestion/utilisateur/{id}/disable", name="nao_utilisateur_disable")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function disableUtilisateurAction($id)
    {
        $utilisateur = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $userManager = $this->get('fos_user.user_manager');
        $utilisateur->setEnabled(false);
        $userManager->updateUser($utilisateur);

        $this->addFlash("success", "L'utilisateur a bien été désactivé.");

        return $this->redirectToRoute('nao_utilisateur_gestion');
    }

    /**
     * @Route("/gestion/utilisateur/{id}/enable", name="nao_utilisateur_enable")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function enableUtilisateurAction($id)
    {
        $utilisateur = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $userManager = $this->get('fos_user.user_manager');
        $utilisateur->setEnabled(true);
        $userManager->updateUser($utilisateur);

        $this->addFlash("success", "L'utilisateur a bien été activé.");

        return $this->redirectToRoute('nao_utilisateur_gestion');
    }

}

