<?php

namespace Editique\TitreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $titreManager = $this->get('editique.titreManager');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $em = $this->getDoctrine()->getManager('bfi2');
        $titreManager->setEntityManager($em);
        $titreManager->setDirSortie($dirSortie);
        $titreManager->initReleve();

        $request = $this->container->get('request');
        $titreExemple = $request->request->get('file');

        if ($titreExemple) {
            if ($fileName = $titreManager->ecrireSortie($dirSortie, $titreExemple)) {
                $headers = array(
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                );

                return new Response(file_get_contents("/app/exchange/editique/out/" . $fileName), 200, $headers);
            }
        }

        return $this->render('EditiqueTitreBundle:Default:index.html.twig');
    }
}
