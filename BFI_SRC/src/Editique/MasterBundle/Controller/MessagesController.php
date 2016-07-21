<?php

namespace Editique\MasterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Editique\MasterBundle\Entity\MessageCommercial;

class MessagesController extends Controller
{
    public function listAction(Request $request)
    {
        $datas = null;
        $repo = $this->getDoctrine()->getManager()->getRepository('EditiqueMasterBundle:MessageCommercial');
        
        if ($datas = $request->request->get('search')) {
            $messages = $repo->search($datas);
        } else {
            $messages = $repo->findAll();
        }
        
        return $this->render('EditiqueMasterBundle:Messages:list.html.twig', array(
            'messages' => $messages,
            'datas'    => $datas
        ));
    }
    
    public function newAction()
    {
        return $this->render('EditiqueMasterBundle:Messages:new.html.twig');
    }
    
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->container->get('backoffice_parser.ecritureManager');
        $datas = $request->request->get('message');
        
        if ($datas['message'] && $datas['dateDebut'] && $datas['dateFin']) {
            $message = new MessageCommercial();
            
            $message
                ->setMessage($datas['message'])
                ->setDateDebut($pm->transformDate($datas['dateDebut']))
                ->setDateFin($pm->transformDate($datas['dateFin']))
                ->setDateCreation(new \Datetime())
                ->setCreateur($this->get('security.context')->getToken()->getUser());
            
            $em->persist($message);
            $em->flush();
            
            return $this->redirect($this->generateUrl('editique_messages_commerciaux'));
        }
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('EditiqueMasterBundle:MessageCommercial')->find($id);
        
        if (!$message) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible d\'accéder à ce message. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('editique_messages_commerciaux'));
        }
        
        return $this->render('EditiqueMasterBundle:Messages:show.html.twig', array(
            'message' => $message
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('EditiqueMasterBundle:MessageCommercial')->find($id);
        
        if (!$message) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible d\'accéder à ce message. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('editique_messages_commerciaux'));
        }
        
        return $this->render('EditiqueMasterBundle:Messages:edit.html.twig', array(
            'message' => $message
        ));
    }
    
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->container->get('backoffice_parser.ecritureManager');
        $message = $em->getRepository('EditiqueMasterBundle:MessageCommercial')->find($id);
        $datas = $request->request->get('message');
        
        if (!$message) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible d\'accéder à ce message. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('editique_messages_commerciaux'));
        }
        
        if ($datas['message'] && $datas['dateDebut'] && $datas['dateFin']) {
            $message
                ->setMessage($datas['message'])
                ->setDateDebut($pm->transformDate($datas['dateDebut']))
                ->setDateFin($pm->transformDate($datas['dateFin']))
                ->setDateModification(new \Datetime())
                ->setEditeur($this->get('security.context')->getToken()->getUser());
            
            $em->persist($message);
            $em->flush();
        }
        
        return $this->redirect(
            $this->generateUrl('editique_messages_commerciaux_show', array('id' => $message->getId()))
        );
    }
}
