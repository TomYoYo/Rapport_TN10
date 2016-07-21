<?php

namespace Editique\MasterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $entities = false;
        $idClient = $request->request->get('idClient');
        $numCpt = $request->request->get('numCpt');

        if ($idClient !== null || $numCpt !== null) {
            $repo = $this->getDoctrine()->getManager()->getRepository('EditiqueMasterBundle:Editique');
            $entities = $repo->getDoc($idClient, $numCpt);
        }
        return $this->render('EditiqueMasterBundle:Default:index.html.twig', array(
                'numCpt'   => $numCpt,
                'idClient' => $idClient,
                'entities' => $entities,
                'erreur'   => 'none'
        ));
    }
    
    public function testAction()
    {
        return $this->render('EditiqueMasterBundle:Default:test.html.twig', array());
    }

    public function printAction($id)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('EditiqueMasterBundle:Editique');

        $entity = $repo->find($id);
        $headers = array(
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $entity->getFileName() . '"'
        );

        if (file_exists($entity->getFilePath())) {
            return new Response(file_get_contents($entity->getFilePath()), 200, $headers);
        } else {
            return $this->render('EditiqueMasterBundle:Default:index.html.twig', array(
                'numCpt'   => '',
                'idClient' => '',
                'entities' => '',
                'erreur'   => 'file_not_exist'
            ));
        }
    }

    public function printTestAction($fileName)
    {
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        if ($fileName != '') {
            $headers = array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            );

            return new Response(file_get_contents($dirSortie . $fileName), 200, $headers);
        }
    }

    public function printTXTTestAction($fileName)
    {
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        if ($fileName != '') {
            $headers = array(
                'Content-Type'        => 'text/plain',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            );

            return new Response(file_get_contents($dirSortie . $fileName), 200, $headers);
        }
    }
    
    public function showPDFAction($name)
    {
        $fichier = $name . ".pdf";
        $chemin = __DIR__ . "/../../CreditBundle/Resources/pdf_template/";

        $response = new Response();
        $response->setContent(file_get_contents($chemin.$fichier));
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-disposition', 'filename='. $fichier);

        return $response;
    }
}
