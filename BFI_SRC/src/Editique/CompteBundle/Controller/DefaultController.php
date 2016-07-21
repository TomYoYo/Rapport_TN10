<?php

namespace Editique\CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // Elements de connexion à Sab32
        $dirSortie = $this->container->getParameter('dirSortieEditique');
        $dirEntree = $this->container->getParameter('dirEntreeEditique');

        // Init
        $compteManager = $this->get('editique.compteManager');
        $fileNameTxt = null;
        $fileNamePdf = null;
        
        // Requête
        $request = $this->container->get('request');
        $fileExample = $request->request->get('file');
        
        // fichier XML dispo
        $tabFileTest = glob($dirEntree . 'ServicesGcsDRPT_*.xml');
        
        if ($fileExample) {
            // Lecture du XML en entrée
            $compteManager->readFile($fileExample);
            
            if (!empty($compteManager->listeSouscriptions)) {
                foreach ($compteManager->listeSouscriptions as $souscription) {
                    $compteManager->setData($souscription);
                    if ($compteManager->doSouscription()) {
                        list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'souscription');
                        $fileNameTxt [] = array('file' => $txt, 'size' => filesize($dirSortie . $txt));
                        $fileNamePdf [] = array('file' => $pdf, 'size' => filesize($dirSortie . $pdf));
                    }
                    if ($compteManager->doSouscriptionLetter()) {
                        list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'club');
                        $fileNameTxt [] = array('file' => $txt, 'size' => filesize($dirSortie . $txt));
                        $fileNamePdf [] = array('file' => $pdf, 'size' => filesize($dirSortie . $pdf));
                    }
                    if ($compteManager->doMdpLetter()) {
                        list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'mdp');
                        $fileNameTxt [] = array('file' => $txt, 'size' => filesize($dirSortie . $txt));
                        $fileNamePdf [] = array('file' => $pdf, 'size' => filesize($dirSortie . $pdf));
                    }
                }
            } else {
                $fileNameTxt = null;
                $fileNamePdf = null;
            }
        }

        return $this->render('EditiqueCompteBundle:Default:index.html.twig', array(
            'fileNameTxt' => $fileNameTxt,
            'fileNamePdf' => $fileNamePdf,
            'tabFileTest' => $tabFileTest,
            'fichierEntree' => $fileExample,
        ));
    }
}
