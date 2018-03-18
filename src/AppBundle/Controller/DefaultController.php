<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\ImportTaxRef;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
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
}
