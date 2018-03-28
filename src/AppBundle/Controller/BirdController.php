<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 24/03/2018
 * Time: 17:32
 */

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\ImportTaxRef;

class BirdController extends Controller
{

    /**
     * @Route("/importTaxref", name="importTaxref")
     */
    public function importTaxrefAction(Request $request, ImportTaxRef $taxRef)
    {

        $datas = $taxRef->import();

        return new Response($datas);
    }

    /**
     * @Route("/bird/search", name="bird.search", methods="POST")
     */
    public function searchBirdByNameAction(Request $request)
    {
        $em             = $this->getDoctrine()->getManager();
        $name           = $request->request->get('term');
        $result         = array();

        $names   = $em->getRepository('AppBundle:Bird')->byNomCourant($name);

        foreach ($names as $value){
            $result[] = array(
                'id'    => $value['id'],
                'label'  => $value['nomCourant'] .' ('.$value['nomScientif'].')',
                'value'    => $value['nomCourant'] .' ('.$value['nomScientif'].')',
            );
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/bird/search/{id}", name="bird.search.id", requirements={"id": "\d+"})
     */
    public function searchBirdByIdAction($id = null)
    {
        $bird   = $this->getDoctrine()->getRepository('AppBundle:Bird')->find($id);

        if ($bird) {
            $name = $bird->getNomCourant() .' ('.$bird->getNomScientif().')';
            return new Response($name);

        }

        return new Response('');
    }

}