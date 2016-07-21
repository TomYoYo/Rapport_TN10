<?php

namespace BackOffice\HabilitationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


class DetailHabilitationController extends Controller
{
    public function indexAction()
    {
        $manager = $this->get('backoffice.userManager');
        /*$metiers = $manager->getHabilitation(4);
        $donnees = $manager->getHabilitation(3);
        $menus = $manager->getHabilitation(2);*/

        $form = $this->createFormBuilder(

        )->add('groupe','choice',array())->getForm();

        return $this->render('BackOfficeHabilitationBundle:Detail:index.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function menuAction()
    {
        $request = $this->get('request');
        $menu = $request->get('menu');
        $parent = $request->get('parent');
        $manager = $this->get('backoffice.habilitationmanager');
        $menus = $manager->getMenus($menu,$parent);
        $response = new Response();

        $data = json_encode($menus); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);

        return $response;

    }

    public function choiceGroupeAction()
    {
        $request = $this->get('request');
        $groupe = $request->get('groupe');
        $manager = $this->get('backoffice.userManager');

        $data_from_scratch = $manager->getHabilitation($groupe);
        $response = new Response();

        $data = json_encode($data_from_scratch); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);

        return $response;
    }

    public function testAction()
    {
        $manager = $this->get('backoffice.userManager');
        echo $manager->test();
        //return $this->redirect($this->generateUrl('back_office_habilitation'));
    }

    public function donneesAction()
    {
        $request = $this->get('request');
        $menu = $request->get('menu');
        $manager = $this->get('backoffice.habilitationmanager');
        $donnees = $manager->getDonnees($menu);
        $response = new Response();
        $data = json_encode($donnees); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);

        return $response;
    }

    public function donneesEachAction()
    {
        $request = $this->get('request');
        $donnee = $request->get('donnee');
        $menu = $request->get('menu');
        $manager = $this->get('backoffice.habilitationmanager');
        $donnees = $manager->getDonneesEach($donnee,$menu);
        $response = new Response();
        $data = json_encode($donnees); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setContent($data);

        return $response;
    }


    public function searchLibAction()
    {
        $request = $this->get('request');
        $donnee = $request->get('donnee');
        $manager = $this->get('backoffice.habilitationmanager');
        $donnees = $manager->searchLib($donnee);
        $response = new Response();
        $data = json_encode($donnees); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);

        return $response;
    }

}
