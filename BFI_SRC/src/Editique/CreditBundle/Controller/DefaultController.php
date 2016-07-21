<?php

namespace Editique\CreditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $echeancierManager = $this->get('editique.echeancierManager');
        $dirSortie = $this->container->getParameter('dirSortieEditique');
        $xmlTest = $this->container->getParameter('dirEntreeEditique').'echeancier-47.xml';
        $xmlTest2 = $this->container->getParameter('dirEntreeEditique').'echeancier-110.xml';
        $xmlTest3 = $this->container->getParameter('dirEntreeEditique').'echeancier-180.xml';
        $xmlTest4 = $this->container->getParameter('dirEntreeEditique').'echeancier-0.xml';
        $txtTest = $this->container->getParameter('dirEntreeEditique').'echeancier-47.txt';
        $txtTest2 = $this->container->getParameter('dirEntreeEditique').'echeancier-110.txt';
        $txtTest3 = $this->container->getParameter('dirEntreeEditique').'echeancier-180.txt';
        $txtTest4 = $this->container->getParameter('dirEntreeEditique').'echeancier-multi-feuillet.txt';
        $txtTest5 = $this->container->getParameter('dirEntreeEditique').'echeancier-multi-feuillet-4.txt';
        $txtTest6 = $this->container->getParameter('dirEntreeEditique').'echeancier-trim.txt';
        $txtTest7 = $this->container->getParameter('dirEntreeEditique').'echeancier-annuel.txt';

        $fileNamePDF = $fileNameTxt = array();
        $em = $this->getDoctrine()->getManager('bfi2');
        $echeancierManager->setEntityManager($em);

        $request = $this->container->get('request');
        $typeSpool = $request->request->get('type');

        if ($typeSpool) {
            $echeancierManager->typeSpool = substr($typeSpool, 0, 3);
            if ($typeSpool == 'XML1') {
                $echeancierManager->lireContent($xmlTest);
            } elseif ($typeSpool == 'XML2') {
                $echeancierManager->lireContent($xmlTest2);
            } elseif ($typeSpool == 'XML3') {
                $echeancierManager->lireContent($xmlTest3);
            } elseif ($typeSpool == 'XML4') {
                $echeancierManager->lireContent($xmlTest4);
            } elseif ($typeSpool == 'TXT1') {
                $echeancierManager->lireContent($txtTest);
            } elseif ($typeSpool == 'TXT2') {
                $echeancierManager->lireContent($txtTest2);
            } elseif ($typeSpool == 'TXT3') {
                $echeancierManager->lireContent($txtTest3);
            } elseif ($typeSpool == 'TXT4') {
                $echeancierManager->lireContent($txtTest4);
            } elseif ($typeSpool == 'TXT5') {
                $echeancierManager->lireContent($txtTest5);
            } elseif ($typeSpool == 'TXT6') {
                $echeancierManager->lireContent($txtTest6);
            } elseif ($typeSpool == 'TXT7') {
                $echeancierManager->lireContent($txtTest7);
            } elseif ($typeSpool == 'BDD1') {
                $echeancierManager->lireContent(2);
            }
            
            $pdf = $echeancierManager->ecrireSortie($dirSortie);
            if ($pdf !== false) {
                $txt = $echeancierManager->getFileName('txt');
                $fileNameTxt [] = array(
                    'file' => $txt,
                    'size' => filesize($dirSortie . $txt)
                );

                $fileNamePDF [] = array(
                    'file' => $pdf,
                    'size' => filesize($dirSortie . $pdf)
                );
            }
        }
        
        // on affiche quelques info sur ce test
        return $this->render('EditiqueCreditBundle:Default:index.html.twig', array(
            'errors' => $echeancierManager->getErrors(),
            'fileTXT' => $fileNameTxt,
            'filePDF' => $fileNamePDF,
            'typeSpool' => $typeSpool
        ));
    }
}
