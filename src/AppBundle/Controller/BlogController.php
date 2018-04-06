<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;

/**
 * Class BlogController
 *
 * @Route("/news")
 *
 * @package AppBundle\Controller
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="nao_blog")
     */
    public function indexAction(Request $request)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(
                array('status' => array(Post::PUBLISHED, Post::FEATURED)),
                array('status' => 'DESC', 'publishedAt' => 'DESC'),
                10,
                0
            );

        return $this->render('nao/blog/index.html.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * @Route("/supprimer/{id}", name="nao_blog_post_supprimer")
     * Method({"GET", "POST"})
     */
    public function supprimerAction(Request $request, $id)
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('nao_blog');
    }

}