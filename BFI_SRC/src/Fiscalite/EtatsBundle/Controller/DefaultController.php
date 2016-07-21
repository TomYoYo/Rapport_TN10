<?php

namespace Fiscalite\EtatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BackOffice\ActionBundle\Entity\Action;

class DefaultController extends Controller
{
    public function listeAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Actions en cours
        $lastSynchro = $em->getRepository('BackOfficeActionBundle:Action')->findOneBy(
            array('type' => 'FISCALITE', 'module' => 'ETATS'),
            array('dtAction' => 'DESC')
        );

        return $this->render('FiscaliteEtatsBundle:Default:liste.html.twig', array(
            'lastSynchro' => $lastSynchro
        ));
    }
    
    public function synchroAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        // Actions en cours
        $actions = $em->getRepository('BackOfficeActionBundle:Action')->findBy(
            array('type' => 'FISCALITE', 'module' => 'ETATS', 'etat' => 'attente')
        );

        if (!$actions) {
            $action = new Action();

            $action
                ->setType('FISCALITE')
                ->setModule('ETATS')
                ->setNumCpt(0);

            $em->persist($action);
            $em->flush();
            
            $lm->addInfo(
                'Nouvelle demande de mise à jour des états réglementaires.',
                'Fiscalité > Etats',
                'Actualisation des états réglementaires'
            );
        }

        return new Response();
    }
    
    public function nodesAction()
    {
        $dirExchange = $this->container->getParameter('dirExchange');
        $fm = $this->container->get('backoffice_file.fileManager');
        $router = $this->get('router');
        $em = $this->getDoctrine()->getManager();
        $tree = array();

        // Actions en cours
        $lastSynchro = $em->getRepository('BackOfficeActionBundle:Action')->findOneBy(
            array('type' => 'FISCALITE', 'module' => 'ETATS'),
            array('dtAction' => 'DESC')
        );


        if ($lastSynchro->getEtat() == 'OK') {
            $lastSynchro = array(
                'date'=>$lastSynchro->getDtAction()->format('d/m/Y à H:i:s'),
                'etat'=>$lastSynchro->getEtat()
            );
            
            $tree = $fm->mkmap($dirExchange.'/etats-reglementaires', $router);
        }

        if (empty($tree)) {
            $lastSynchro = array(
                'date' => date('d/m/Y à H:i:s'),
                'etat' => $lastSynchro->getEtat()
            );
        }

        $response = json_encode(array('lastSynchro' => $lastSynchro, 'tree' => $tree));

        return new Response($response);
    }
}
