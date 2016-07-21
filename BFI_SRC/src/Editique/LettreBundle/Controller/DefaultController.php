<?php

namespace Editique\LettreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $lettreManager = $this->get('editique.lettreManager');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $em = $this->getDoctrine()->getManager('bfi2');
        $lettreManager->setEntityManager($em);

        $request = $this->container->get('request');
        $lettreExemple = $request->request->get('file');

        switch ($lettreExemple) {
            case 'CHQ':
                if ($lettreManager->prepareLettre(32, $lettreExemple)) {
                    if ($fileName = $lettreManager->ecrireSortie($dirSortie, $lettreExemple)) {
                        $headers = array(
                            'Content-Type'        => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                        );

                        return new Response(
                            file_get_contents("/app/exchange/editique/out/" . $fileName),
                            200,
                            $headers
                        );
                    }
                }
                break;
        }

        return $this->render('EditiqueLettreBundle:Default:index.html.twig');
    }
}
