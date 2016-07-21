<?php

namespace Transverse\PartenaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Transverse\PartenaireBundle\Entity\Parametrage;
use Transverse\PartenaireBundle\Entity\Spool;
use Transverse\PartenaireBundle\Form\ParametrageType;
use Transverse\PartenaireBundle\Form\ParametrageEditType;
use Transverse\PartenaireBundle\Form\RoleType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use BackOffice\ParserBundle\Manager\ParserManager;

class SpoolalertController extends Controller
{
    public function indexAction()
    {
        return $this->render('TransversePartenaireBundle:Spoolalert:index.html.twig');
    }
       


    public function newAction(Request $request)
    {
        $parametrage = new Parametrage();
        $form = $this->get('form.factory')->create(new ParametrageType(), $parametrage);


        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach ($parametrage->getFiltres() as $filtre) {
                if ($filtre != null) {
                    $filtre->setParametrage($parametrage);
                }
            }
            if (isset($filtre) and $filtre != null) {
                $em->persist($filtre);
            }
         
            $em->persist($parametrage);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Paramètre bien enregistré.');
            return $this->redirect(
                $this->generateUrl(
                    'transverse_partenaire_spoolalert_administration_show',
                    array('id' => $parametrage->getId())
                )
            );
        }
        
        
        return $this->render('TransversePartenaireBundle:Spoolalert:Administration/new.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    
    
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parametrage = $em->getRepository('TransversePartenaireBundle:Parametrage')->find($id);

        if (null === $parametrage) {
            throw new NotFoundHttpException("Le paramètre numéro ".$id." n'existe pas.");
        }
         
        $form = $this->get('form.factory')->create(new ParametrageType(), $parametrage);
        $form->handleRequest($request);
        if ($form->isValid()) {
            foreach ($parametrage->getFiltres() as $filtre) {
                if ($filtre != null) {
                    $filtre->setParametrage($parametrage);
                }
            }
            if (isset($filtre) and $filtre != null) {
                $em->persist($filtre);
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Paramètre bien modifié.');
            return $this->redirect(
                $this->generateUrl(
                    'transverse_partenaire_spoolalert_administration_show',
                    array('id' => $parametrage->getId())
                )
            );
        }


        return $this->render('TransversePartenaireBundle:Spoolalert:Administration/edit.html.twig', array(
            'form'   => $form->createView(),
            'parametrage' => $parametrage,
        ));
    }
 
  
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parametrage = $em->getRepository('TransversePartenaireBundle:Parametrage')->find($id);
 
        if (null === $parametrage) {
            throw new NotFoundHttpException("Le paramètre numéro ".$id." n'existe pas.");
        }
        //Champ CSRF
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $em->remove($parametrage);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le paramètrage a bien été supprimé.");

            return $this->redirect($this->generateUrl('transverse_partenaire_spoolalert_administration'));
        }
        //si GET, on affiche une page de confirmation
        return $this->render('TransversePartenaireBundle:Spoolalert:Administration/delete.html.twig', array(
            'parametrage' => $parametrage,
            'form'   => $form->createView()
        ));
    }
    
    
    
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('TransversePartenaireBundle:Parametrage')
        ;
           
        $parametrage = $repository->find($id);

        if (null === $parametrage) {
            throw new NotFoundHttpException("Le paramètre numéro ".$id." n'existe pas.");
        }
        return $this->render('TransversePartenaireBundle:Spoolalert:Administration/show.html.twig', array(
            'parametrage' => $parametrage,
        ));
    }
    
    public function consultationAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        
        $data   = $request->request->get('search');
        
        if (isset($data['dateSpool'])) {
            $dateSpool = ParserManager::transformDateToCYYMMDD($data['dateSpool']);
            $date_format_normal = $data['dateSpool'];

        } else {
            $dateSpool = (date('Y') >= 2000? 1 : 0) . date('ymd');
            $date_format_normal = date('d/m/Y');
        }

        $parametrages = $em->getRepository('TransversePartenaireBundle:Parametrage')->findAll();
     
        return $this->render('TransversePartenaireBundle:Spoolalert:Consultation/index.html.twig', array(
            'parametrages' => $parametrages,
            'dateSpool' => $dateSpool,
            'date_format_normal' => $date_format_normal,
        ));
        
    }
    
    public function spoolshowAction($id)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('TransversePartenaireBundle:Spool')
        ;
        
        $spoolRepertory  = $this->container->getParameter('dirSpools');
        
        $spool = $repository->find($id);
        
        if (null === $spool) {
            throw new NotFoundHttpException("Le spool n'existe pas.");
        }
        $filename = basename($spool->getUrlSpool());


        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($spoolRepertory.$filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', filesize($spoolRepertory.$filename));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($spoolRepertory.$filename));
        return $response;
    }
    
    
    public function administrationAction()
    {
        
        $em = $this->getDoctrine()->getManager();

        $parametrages = $em->getRepository('TransversePartenaireBundle:Parametrage')->findAll();


        return $this->render('TransversePartenaireBundle:Spoolalert:Administration/index.html.twig', array(
            'parametrages' => $parametrages,
            
        ));
    }
}
