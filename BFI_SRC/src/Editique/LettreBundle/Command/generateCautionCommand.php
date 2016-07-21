<?php

namespace Editique\LettreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCautionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:caution')
            ->setDescription('Génére les txt et pdf après être aller chercher les données en BDD.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init
        $manager = $this->getContainer()->get('editique.cautionManager');
        $sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $dirEntree = $this->getContainer()->getParameter('dirEntreeEditique');
        $dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');
        $dirRemote = $this->getContainer()->getParameter('sabCore.dirPathPrintTop');
        
        $regex = "/^CAUED003P3_\w+\.txt$/";
        $files = $sabManager->fichiersMasques($dirRemote, $regex);
        //$files = array('CAUED003P3_1160301131033782225.txt');

        foreach ($files as $file) {
            // On copie tous les fichiers dans in sur bfi server
            $locPath = $dirEntree . $file; // Dossier en interne (exchange)
            $remPath = $dirRemote . $file; // Dossier en externe (SAB)
            $sabManager->download($remPath, $locPath);
            
            // On explose les cautions dans un tableau
            $cautions = $manager->explode($locPath);

            foreach ($cautions as $caution) {
                $manager->reinit();
                
                // On récupère les données (spool et BDD)
                $manager->lireSpool($caution);
                $manager->getDatasFromDB();

                // On écrit la sortie, s'il n'y a ps eu d'erreur
                if ($manager->doWrite) {
                    $manager->ecrireSortie($dirSortie);
                    $manager->integrerPourImpression();
                }
            }

            // On déplace le fichier dans treated (pas de gestion des erreurs)
            $fileManager->deplacerFichier($locPath, $dirTreated . $file);

            // On supprime le fichier de sabcore
            $sabManager->delete($remPath);
        }
        
        $manager->ecrirePDFGlobal($dirSortie);
    }
}
