<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\ObservationType;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


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
    public function ObservationAction()
    {
        $observations = $this->getDoctrine()
            ->getRepository(Observation::class)
            ->findAll();

        return $this->render('nao/observation/observation.html.twig', array(
            'observations' => $observations
        ));
    }


    /**
     * @Route("/observation/carte", name="nao_carte_observation")
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
        /*
        $em             = $this->getDoctrine()->getManager();
        $bird           = $request->request->get('bird');
        $result         = array();

        $names   = $em->getRepository('AppBundle:Observation')->byNomCourant($name);

        foreach ($names as $value){
            $result[] = array(
                'id'    => $value['id'],
                'name'  => $value['nomCourant'] .' ('.$value['nomScientif'].')',
            );
        }
        */

        $bird = $request->request->get('bird');
        if ($bird == 441665)
        {
            $result = array(
                array(
                    'id' => 1,
                    'birdName' => 'Colibri à gorge rubis',
                    'birdId' => 441665,
                    'userName' => 'Bertrand92',
                    'dateObservation' => '2018-03-02',
                    'latitude' => 48.879676,
                    'longitude' => 2.381688,
                ),
                array(
                    'id' => 1,
                    'birdName' => 'Colibri à gorge rubis',
                    'birdId' => 441665,
                    'userName' => 'Lucie',
                    'dateObservation' => '2018-01-02',
                    'latitude' => 48.679676,
                    'longitude' => 2.981688,
                ),
                'info' => '2 résultats',
            );

        } elseif ($bird == 534742) {
            $result = array(
                array(
                    'id' => 1,
                    'birdName' => 'Mésange',
                    'birdId' => 534742,
                    'userName' => 'Pierre',
                    'dateObservation' => '2018-02-02',
                    'latitude' => 48.979676,
                    'longitude' => 2.281688,
                ),

                'info' => '1 résultats',
            );
        } elseif (!empty($bird)) {
            $result = array(
                'info' => 'Aucun résultat',
            );
        } else {
            $result = array(
                array(
                    'id' => 1,
                    'birdName' => 'Colibri à gorge rubis',
                    'birdId' => 441665,
                    'userName' => 'Bertrand92',
                    'dateObservation' => '2018-03-02',
                    'latitude' => 48.879676,
                    'longitude' => 2.381688,
                ),
                array(
                    'id' => 1,
                    'birdName' => 'Mésange',
                    'birdId' => 534742,
                    'userName' => 'Pierre',
                    'dateObservation' => '2018-02-02',
                    'latitude' => 48.979676,
                    'longitude' => 2.281688,
                ),
                array(
                    'id' => 1,
                    'birdName' => 'Colibri à gorge rubis',
                    'birdId' => 441665,
                    'userName' => 'Lucie',
                    'dateObservation' => '2018-01-02',
                    'latitude' => 48.679676,
                    'longitude' => 2.981688,
                ),
                'info' => '3 résultats',
            );
        }


        return new JsonResponse($result);
    }

}

