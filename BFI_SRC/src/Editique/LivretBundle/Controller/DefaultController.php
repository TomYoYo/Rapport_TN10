<?php

namespace Editique\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $livretManager = $this->get('editique.livretManager');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $em = $this->getDoctrine()->getManager('bfi2');
        $livretManager->setEntityManager($em);
        
        $request = $this->container->get('request');
        $livretExemple = $request->request->get('file');
        
        if ($livretExemple) {
            $fileName = $livretManager->ecrireSortie($dirSortie, $livretExemple);
            $headers = array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            );
            return new Response(file_get_contents("/app/exchange/editique/out/".$fileName), 200, $headers);
        }
        
        // Return
        return $this->render('EditiqueLivretBundle:Default:index.html.twig');
    }
}
