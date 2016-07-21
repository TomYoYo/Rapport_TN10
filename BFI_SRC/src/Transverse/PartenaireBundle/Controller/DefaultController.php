<?php

namespace Transverse\PartenaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TransversePartenaireBundle:Default:index.html.twig');
    }
}
