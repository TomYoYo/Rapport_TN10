<?php

namespace Editique\RIBBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller DefaultController. Permet le parsing d'un RIB
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        // Elements de connexion à Sab32
        $exempleLocal = $this->container->getParameter('exempleLocal');
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        // Init
        $filePath = $exempleLocal;
        $ribManager = $this->get('editique.ribManager');

        // Lecture et écriture
        $ribManager->readFile($filePath);

        if ($ribManager->getFatalError() == false) {
            $sortieBrute = $ribManager->ecrireSortie($dirSortie);
            $fileName = $ribManager->getFileName('txt');
        } else {
            $fileName = 'null';
            $sortieBrute = "Une erreur s'est produite :\n";
            foreach ($ribManager->getErrors() as $error) {
                $sortieBrute .= $error . "\n";
            }
        }

        return $this->render('EditiqueRIBBundle:Default:index.html.twig', array(
                'fileName' => $fileName,
                'dir' => $dirSortie,
                'sortieBrute' => str_replace(' ', '<i style="color:#AAA;">#</i>', $sortieBrute)
        ));
    }
}
