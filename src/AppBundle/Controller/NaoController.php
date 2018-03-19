<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Observation;
use AppBundle\Form\ObservationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\ImportTaxRef;

class NaoController extends Controller
{
    /**
     * @Route("/", name="nao_accueil")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('nao/index.html.twig');
    }

    /**
     * @Route("/importTaxref", name="importTaxref")
     */
    public function importTaxrefAction(Request $request, ImportTaxRef $taxRef)
    {

        $datas = $taxRef->import();

        return new Response($datas);
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

            return new Response('Données d\'observation envoyées');
        }

        return $this->render('nao/observation/ajouter.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/observation", name="nao_observation")
     */
    public function Observation()
    {
        return $this->render('nao/observation/observation.html.twig');
    }

}

