<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

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
     * @Route("/detail/{slug}", name="nao_blog_details")
     * @param Request $request
     * @param $slug
     * @return Response
     */
    public function blogDetailsAction(Request $request, $slug)
    {
        $article = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findOneBy(
                array('slug' => $slug)
            );

        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas !');
        }

        $id = $article->getId();

        $prevArticle = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id-1);

        $nextArticle = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id+1);

        // Commentaires
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*
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
            */
        }

        return $this->render('nao/blog/details.html.twig', array(
            'article' => $article,
            'prevArticle' => $prevArticle,
            'nextArticle' => $nextArticle,
            'form' => $form->createView()
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