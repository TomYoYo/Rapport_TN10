<?php

namespace Editique\ReleveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // init
        $releveManager = $this->get('editique.releveManager');
        $dirEntree = $this->container->getParameter('dirEntreeEditique');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $request = $this->container->get('request');
        $fileExample = $request->request->get('file');
        $degradee = $request->request->get('degradee');

        $fileNameTxt = $fileNamePDF = array();
        $nbReleve = 0;

        // fichier texte dispo
        $tabFileTest = glob($dirEntree . '*.txt');

        // On lit le fichier
        if ($fileExample) {
            $releveManager->setDegradee($degradee == 1);
            $releveManager->setDirSortie($dirSortie);
            
            $tabRelevesContent = $releveManager->getRelevesFromFile($fileExample);

            // Pour chaque releve reperé, on ecrit une sortie
            foreach ($tabRelevesContent as $i => $releveContent) {
                if (trim($releveContent) != '') {
                    
                    $releveManager->setContent($releveContent);

                    // si on est sur un nouveau releve
                    if ($releveManager->getNumFeuillet() == 1) {
                        // s'il y avait deja un releve en cours
                        if ($releveManager->getOReleve() != null) {
                            if ($releveManager->ecrireSortie()) {
                                $txt = $releveManager->getFileName('txt');
                                $fileNameTxt [] = array(
                                    'file' => $txt,
                                    'size' => filesize($dirSortie . $txt)
                                );
                                $pdf = $releveManager->getFileName('pdf');
                                $fileNamePDF [] = array(
                                    'file' => $pdf,
                                    'size' => filesize($dirSortie . $pdf)
                                );
                            }
                        }

                        // on initialise le releve
                        $releveManager->initReleve();
                    }

                    // on lit le contenu du releve
                    $releveManager->lireContent();
                }
                
                // s'il y avait deja un releve en cours avec plusieurs feuillets
                if ($releveManager->getOReleve() != null) {
                    if ($releveManager->ecrireSortie()) {
                        $txt = $releveManager->getFileName('txt');
                        $fileNameTxt [] = array(
                            'file' => $txt,
                            'size' => filesize($dirSortie . $txt)
                        );
                        $pdf = $releveManager->getFileName('pdf');
                        $fileNamePDF [] = array(
                            'file' => $pdf,
                            'size' => filesize($dirSortie . $pdf)
                        );
                    }
                }
                
                $nbReleve++;
            }
        }

        // on affiche quelques info sur ce test
        return $this->render('EditiqueReleveBundle:Default:index.html.twig', array(
            'fichierEntree' => $fileExample,
            'nbReleve' => ($nbReleve),
            'contentSortie' => $releveManager->ecritureManager->getContentHTML(),
            'errors' => $releveManager->getErrors(),
            'tabFileTest' => $tabFileTest,
            'selectedFile' => basename($fileExample),
            'degradee' => $degradee,
            'fileTXT' => $fileNameTxt,
            'filePDF' => $fileNamePDF
        ));
    }
}
