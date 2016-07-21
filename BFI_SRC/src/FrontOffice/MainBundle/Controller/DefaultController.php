<?php

namespace FrontOffice\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BackOffice\ParserBundle\Manager\ParserManager;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrontOfficeMainBundle:Default:index.html.twig', array());
    }

    public function printFileAction($filePath)
    {
        if ($filePath != '') {

            $filePath = base64_decode($filePath);

            $fileName = basename($filePath);

            $contentType = 'text/plain';
            if (ParserManager::endsWith($fileName, '.pdf')) {
                $contentType = 'application/pdf';
            }
            if (ParserManager::endsWith($fileName, '.xml')) {
                $contentType = 'application/xml';
            }

            $headers = array(
                'Content-Type'        => $contentType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            );

            return new Response(file_get_contents($filePath), 200, $headers);
        }
    }

    public function oldBrowserAction()
    {
        return $this->render('FrontOfficeMainBundle:Default:oldBrowser.html.twig', array());
    }
}
