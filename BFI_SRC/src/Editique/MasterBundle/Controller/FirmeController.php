<?php

namespace Editique\MasterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Editique\MasterBundle\Entity\RelationFirme;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class FirmeController extends Controller
{
    public function indexAction(Request $request)
    {
        $datas = null;
        $repo = $this->getDoctrine()->getManager()->getRepository('EditiqueMasterBundle:RelationFirme');
        
        if ($datas = $request->request->get('search')) {
            $relations = $repo->search($datas);
        } else {
            $relations = $repo->findAll();
        }
        
        return $this->render('EditiqueMasterBundle:Firme:index.html.twig', array(
            'relations' => $relations,
            'datas'    => $datas
        ));
    }
    
    public function searchAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repo = $entityManager->getRepository('EditiqueMasterBundle:RelationFirme');
        $search = $request->get('search');
        $relationFirme = $repo->findOneBy(array('idBfi' => $this->getUser()->getId()));
        
        if ($search) {
            if (!$relationFirme || !in_array($search['numClient'], $relationFirme->getIdTiers())) {
                $this->container->get('session')->getFlashBag()->add(
                    'error',
                    'Vous n\'êtes pas autorisés à consulter les documents pour ce client.'
                );

                return $this->redirect($this->generateUrl('editique_releves_firme_search'));
            }
            
            $datas = array(
                'idClient' => $search['numClient'],
                'numCpt' => $search['numCpt'],
                'typeDoc' => 'releve_quotidien'
            );
            
            $entities = $entityManager->getRepository('EditiqueMasterBundle:Editique')->search($datas);
        } else {
            if (!$relationFirme) {
                $entities = null;
            } else {
                $entities = $entityManager->getRepository('EditiqueMasterBundle:Editique')->getAllFirme(
                    $relationFirme->getIdTiers()
                );
            }
        }
        
        if ($entities) {
            $adapter = new ArrayAdapter($entities);
            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta->setMaxPerPage(10);
            $page = $request->query->get('page') ? $request->query->get('page') : 1;
            $pagerfanta->setCurrentPage($page);
        } else {
            $pagerfanta = null;
        }
        
        $relation = $repo->findByIdBfi($this->getUser());
        
        return $this->render('EditiqueMasterBundle:Firme:search.html.twig', array(
            'relations' => $relation,
            'entities' => $pagerfanta,
            'datas' => $search
        ));
    }
    
    public function newAction()
    {
        $profilRepo = $this->getDoctrine()->getManager()->getRepository('BackOfficeUserBundle:Profil');
        $firmeUsers = $profilRepo->search(array('role' => 'ROLE_FIRME'));
        
        return $this->render('EditiqueMasterBundle:Firme:new.html.twig', array(
            'firmeUsers' => $firmeUsers
        ));
    }
    
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if ($request->get('user') != null && $request->get('tiers') != null) {
            $relationFirme = new RelationFirme();
            
            $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($request->get('user'));
            
            $relationFirme
                ->setIdBfi($user)
                ->setIdTiers(array_filter($request->get('tiers')))
                ->setInformations($request->get('infos'))
            ;
            
            $em->persist($relationFirme);
            $em->flush();
            
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Relation créée avec succès.'
            );
            
            return $this->redirect($this->generateUrl('editique_releves_firme'));
        }
        
        $this->container->get('session')->getFlashBag()->add(
            'error',
            'Erreur lors de la saisie. La relation n\'a pas été créée.'
        );
        
        return $this->redirect($this->generateUrl('editique_releves_firme_new'));
    }
    
    public function editAction($id)
    {
        $profilRepo = $this->getDoctrine()->getManager()->getRepository('BackOfficeUserBundle:Profil');
        $relationRepo = $this->getDoctrine()->getManager()->getRepository('EditiqueMasterBundle:RelationFirme');
        $firmeUsers = $profilRepo->search(array('role' => 'ROLE_FIRME'));
        $relationFirme = $relationRepo->find($id);
            
        if (!$relationFirme) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Relation inexistante. Celle-ci ne peut pas être modifiée.'
            );

            return $this->redirect($this->generateUrl('editique_releves_firme'));
        }
        
        return $this->render('EditiqueMasterBundle:Firme:edit.html.twig', array(
            'firmeUsers' => $firmeUsers,
            'relation' => $relationFirme
        ));
    }
    
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if ($request->get('user') != null && $request->get('tiers') != null) {
            $relationFirme = $em->getRepository('EditiqueMasterBundle:RelationFirme')->find($id);
            
            if (!$relationFirme) {
                $this->container->get('session')->getFlashBag()->add(
                    'error',
                    'Relation inexistante. Celle-ci ne peut pas être modifiée.'
                );

                return $this->redirect($this->generateUrl('editique_releves_firme'));
            }
        
            $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($request->get('user'));
            
            $relationFirme
                ->setIdBfi($user)
                ->setIdTiers(array_filter($request->get('tiers')))
                ->setInformations($request->get('infos'))
            ;
            
            $em->persist($relationFirme);
            $em->flush();
            
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Relation modifiée avec succès.'
            );
            
            return $this->redirect($this->generateUrl('editique_releves_firme'));
        }
        
        $this->container->get('session')->getFlashBag()->add(
            'error',
            'Erreur lors de la saisie. La relation n\'a pas été modifiée.'
        );
        
        return $this->redirect($this->generateUrl('editique_releves_firme_new'));
    }
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $relationFirme = $em->getRepository('EditiqueMasterBundle:RelationFirme')->find($id);
        
        if (!$relationFirme) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Relation inexistante. Celle-ci ne peut pas être supprimée.'
            );
        
            return $this->redirect($this->generateUrl('editique_releves_firme'));
        }
        
        $em->remove($relationFirme);
        $em->flush();
        
        $this->container->get('session')->getFlashBag()->add(
            'info',
            'Relation supprimée avec succès.'
        );
        
        return $this->redirect($this->generateUrl('editique_releves_firme'));
    }
}
