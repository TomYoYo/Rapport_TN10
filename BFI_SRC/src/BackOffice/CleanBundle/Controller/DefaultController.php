<?php

namespace BackOffice\CleanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $regles = $em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->findAll();

        $param = array(
            'regles' => $regles
        );

        return $this->render('BackOfficeCleanBundle:Default:index.html.twig', $param);
    }

    public function listingDANAction()
    {
        $tabDirDAN = $this->container->getParameter('sabCore.DAN');
        $fm = $this->container->get('backoffice_file.fileManager');
        $router = $this->get('router');
        $tree = array();
        foreach ($tabDirDAN as $dir) {
            //$tree += $fm->mkmap($dir, $router);
        }
        $response = json_encode($tree);

        return new Response($response);
    }
}
