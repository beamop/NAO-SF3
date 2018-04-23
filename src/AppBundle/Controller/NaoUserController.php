<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NaoUserController extends Controller
{
    /**
     * @Route("/profil", name="nao_profile")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllValidatedBirds();

        return $this->render('naouser/profile.html.twig', array(
            'observations' => $observations,
            'user' => $user
        ));
    }

    /**
     * @Route("/gestion", name="nao_admin")
     */
    public function adminAction()
    {
        $observationsOnHold = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllNoValidatedBirds();

        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAllValidatedBirds();

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(
                array('status' => array(Post::PUBLISHED, Post::FEATURED)),
                array('status' => 'DESC', 'publishedAt' => 'DESC'),
                10,
                0
            );

        return $this->render('naouser/admin/admin.html.twig', array(
            'observationsOnHold' => $observationsOnHold,
            'observations' => $observations,
            'posts' => $posts
        ));
    }

}