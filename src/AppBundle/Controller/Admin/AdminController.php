<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Observation;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/gestion/utilisateur/{id}/disable", name="nao_utilisateur_disable")
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

