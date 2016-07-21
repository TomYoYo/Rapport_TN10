<?php

namespace Editique\DATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $datManager = $this->get('editique.datManager');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $em = $this->getDoctrine()->getManager('bfi2');
        $datManager->setEntityManager($em);

        $request = $this->container->get('request');
        $datExemple = $request->request->get('file');

        if ($datExemple) {
            if ($fileName = $datManager->ecrireSortie($dirSortie, $datExemple)) {
                $headers = array(
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                );

                return new Response(file_get_contents("/app/exchange/editique/out/" . $fileName), 200, $headers);
            }
        }

        return $this->render('EditiqueDATBundle:Default:index.html.twig');
    }
}
