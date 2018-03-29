<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\ObservationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Bird;


class NaoController extends Controller
{
    /**
     * @Route("/", name="nao_accueil")
     */
    public function indexAction(Request $request)
    {
        return $this->render('nao/index.html.twig');
    }

    /**
     * @Route("/observation/ajouter", name="nao_ajouter_observation")
     */
    public function AjouterObservationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $observation = new Observation();

        $form = $this->createForm(ObservationType::class, $observation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $this->get('security.token_storage')->getToken()->getUser();
            $observation->setUtilisateur($utilisateur);

            $em->persist($observation);
            $em->flush();

            return $this->redirectToRoute('nao_observation');
        }

        return $this->render('nao/observation/ajouter.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/observation/liste", name="nao_liste_observation")
     */
    public function ListeObservationAction()
    {
        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAll();

        return $this->render('nao/observation/liste.html.twig', array(
            'observations' => $observations
        ));
    }


    /**
     * @Route("/observation", name="nao_observation")
     */
    public function carteObservationAction()
    {
        return $this->render('nao/observation/carte.html.twig');
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

            $observations   = $this->getDoctrine()
                ->getRepository(Observation::class)
                ->byBirdId($birdId);

        } else {
            $observations   = $this->getDoctrine()
                ->getRepository(Observation::class)
                ->findAll();
        }

        foreach ($observations as $obs){
            $result[] = array(
                'id'    => $obs->getId(),
                'userName' => $obs->getUtilisateur(),
                'birdName'  => $obs->getBird()->getNomCourant(),
                'dateObservation' => $obs->getDate()->format('d/m/Y'),
                'latitude' => $obs->getLatitude(),
                'longitude' => $obs->getLongitude(),
            );
            $nb++;
        }

        $result['info'] = $nb . ' observations trouvées.';

        return new JsonResponse($result);
    }

}

