<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BackOffice\ActionBundle\Entity\Action;

class SABController extends Controller
{
    /*
     * Liste les fichiers de logs de sab
     */
    public function logsAction()
    {
        return $this->liste('logs');
    }

    /*
     * Liste les fichiers de l'arbo de sab
     */
    public function arboAction()
    {
        return $this->liste('arbo');
    }

    private function liste($type)
    {
        $em = $this->getDoctrine()->getManager();

        // Actions en cours
        $lastSynchro = $em->getRepository('BackOfficeActionBundle:Action')->findOneBy(
            array('type' => 'SAB', 'module' => strtoupper($type)),
            array('dtAction' => 'DESC')
        );

        return $this->render('BackOfficeMonitoringBundle:SAB:'.strtolower($type).'.html.twig', array(
            'lastSynchro'=>$lastSynchro
        ));
    }


    public function logsSynchroAction()
    {
        return $this->arboSynchro('logs');
    }

    public function arboSynchroAction()
    {
        return $this->arboSynchro('arbo');
    }

    private function arboSynchro($type)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        // Actions en cours
        $actions = $em->getRepository('BackOfficeActionBundle:Action')->findBy(
            array('type' => 'SAB', 'module' => strtoupper($type), 'etat' => 'attente')
        );

        if (!$actions) {
            $action = new Action();

            $action
                ->setType('SAB')
                ->setModule(strtoupper($type))
                ->setNumCpt(0);

            $em->persist($action);
            $em->flush();

            if ($type == 'arbo') {
                $lm->addInfo(
                    'Nouvelle demande de mise à jour de l\'arbo SAB.',
                    'BackOffice > Monitoring',
                    'Actualisation de l\'arbo SAB'
                );
            } else {
                $lm->addInfo(
                    'Nouvelle demande de mise à jour de l\'arbo SAB.',
                    'BackOffice > Monitoring',
                    'Actualisation de l\'arbo SAB'
                );
            }
        }

        return new Response();
    }

    public function logsNodesAction()
    {
        return $this->arboNodes('logs');
    }

    public function arboNodesAction()
    {
        return $this->arboNodes('arbo');
    }

    private function arboNodes($type)
    {
        $dirExchange = $this->container->getParameter('dirExchange');
        $fm = $this->container->get('backoffice_file.fileManager');
        $router = $this->get('router');
        $em = $this->getDoctrine()->getManager();
        $tree = array();

        // Actions en cours
        $lastSynchro = $em->getRepository('BackOfficeActionBundle:Action')->findOneBy(
            array('type' => 'SAB', 'module' => strtoupper($type)),
            array('dtAction' => 'DESC')
        );


        if ($lastSynchro->getEtat() == 'OK') {
            $lastSynchro = array(
                'date'=>$lastSynchro->getDtAction()->format('d/m/Y à H:i:s'),
                'etat'=>$lastSynchro->getEtat()
            );
            if ($type == 'arbo') {
                $tree = $fm->mkmap($dirExchange.'/arboSab', $router);
            } else {
                $tree = $fm->mkmap($dirExchange.'/sab', $router);
            }
        }

        if (empty($tree)) {
            $lastSynchro = array(
                'date'=>date('d/m/Y à H:i:s'),
                'etat'=>$lastSynchro->getEtat()
            );
        }

        $response = json_encode(array('lastSynchro'=>$lastSynchro, 'tree'=>$tree));

        return new Response($response);
    }
}
