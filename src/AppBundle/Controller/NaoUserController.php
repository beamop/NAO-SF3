<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NaoUserController extends Controller
{
    /**
     * @Route("/tableau-de-bord/", name="nao_redirect_to_profile")
     */
    public function redirectToProfileAction()
    {
        return new Response($this->redirectToRoute('nao_profile'));
    }

    /**
     * @Route("/tableau-de-bord/profile", name="nao_profile")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profileAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager
            ->findUsers();

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAll();

        return $this->render('naouser/profile.html.twig', array(
            'observations' => $observations,
            'user' => $user
        ));
    }

    /**
     * @Route("/tableau-de-bord/administration", name="nao_admin")
     */
    public function adminAction()
    {
        return new Response('Administration');
    }

}