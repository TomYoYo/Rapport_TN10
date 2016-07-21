<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    public $tree = "";
    public $parentDir = "";

    public function listModulesAction()
    {
        $params = $this->container->getParameter('modules');
        return new Response(json_encode($params));
    }

    public function listNodesAction(Request $request)
    {
        $dirExchange = $this->container->getParameter('dirExchange');
        $fm = $this->container->get('backoffice_file.fileManager');
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $router = $this->get('router');
        $tree = $fm->mkmap($dirExchange, $router);
        $response = json_encode($tree);
        
        if ($response === false) {
            $lm->addError(
                "Erreur JSON sur l'affichage de l'arborescence : " . json_last_error(),
                "Back-Office > Monitoring",
                "Affichage arborescence"
            );
            return new Response(json_encode('erreur'));
        }

        return new Response($response);
    }
}
