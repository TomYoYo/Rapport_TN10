<?php

namespace Editique\CompteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:scc')
            ->setDescription('Génére les txt et pdf après être aller chercher le fichiers source sur sabCore.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init Managers
        $compteManager = $this->getContainer()->get('editique.compteManager');
        $logManager    = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fileManager   = $this->getContainer()->get('backoffice_file.fileManager');
        $sabManager    = $this->getContainer()->get('back_office_connexion.SabSFTP');
        
        // JD : c'est pas jojo ce que j'ai fait, il faudrait faire cela autrement... singleton ?
        $logManager->setProcessMode(true);
        $compteManager->logManager->setProcessMode(true);
        $compteManager->windowsFTPManager->logManager->setProcessMode(true);
        $fileManager->logManager->setProcessMode(true);
        $sabManager->logManager->setProcessMode(true);
        
        // Init répertoires
        $dirEntree = $this->getContainer()->getParameter('dirEntreeEditique');
        $dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');
        $dirRemote = $this->getContainer()->getParameter('sabCore.dirPathXML');
        $subDir = $this->getContainer()->getParameter('sabCore.subDirXML');
        
        // Init variables
        $count = 0;
        $countOk = 0;
        $countKo = 0;
        $regex = "/^ServicesGcsDRPT_\d{4}-\d{2}-\d{2}\ \d{2}-\d{2}-\d{2}-\d{3}.xml$/";
        
        // connexion sabCore
        $filesInDirectory = $sabManager->fichiersSousDossiersMasques($dirRemote, $subDir, $regex);
        
        foreach ($filesInDirectory as $pathDirectory => $files) {
            foreach ($files as $file) {
                $count++;
                
                // REINIT
                $compteManager->reinit();
                
                // On copie tout les fichiers dans in sur bfi server
                $locpath = $dirEntree . $file;
                $rempath = $pathDirectory . $file;
                
                $sabManager->download($rempath, $locpath);
                
                // On lit le fichier
                $compteManager->readFile($locpath);
                
                if (!empty($compteManager->listeSouscriptions)) {
                    foreach ($compteManager->listeSouscriptions as $souscription) {
                        $compteManager->setData($souscription);
                        if ($compteManager->doSouscription()
                        || $compteManager->doSouscriptionLetter()
                        || $compteManager->doMdpLetter()) {
                            if ($compteManager->doSouscription()) {
                                list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'souscription');
                                $sizeTXT = filesize($dirSortie . $txt);
                                $sizePDF = filesize($dirSortie . $pdf);
                            }
                            if ($compteManager->doSouscriptionLetter()) {
                                list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'club');
                                $sizeTXT = filesize($dirSortie . $txt);
                                $sizePDF = filesize($dirSortie . $pdf);
                            }
                            if ($compteManager->doMdpLetter()) {
                                list($txt, $pdf) = $compteManager->ecrireSortie($dirSortie, 'mdp');
                                $sizeTXT = filesize($dirSortie . $txt);
                                $sizePDF = filesize($dirSortie . $pdf);
                            }

                            // Déplacement de in vers treated ou error
                            if ($sizeTXT > 0 && $sizePDF > 0) {
                                $countOk++;
                                // Déplacement dans treated
                                $fileManager->deplacerFichier($locpath, $dirTreated . $file);
                            } else {
                                $countKo++;
                                $logManager->addAlert(
                                    'SCC ' . $file . ' vide ; déplacé dans le dossier "error".',
                                    'Editique > Compte',
                                    'Commande de génération des SCC'
                                );
                                $fileManager->deplacerFichier($locpath, $dirError . $file);
                            }

                            // On supprime le fichier de sabcore
                            $sabManager->delete($rempath);
                        } else {
                            $fileManager->deplacerFichier($locpath, $dirTreated . $file);
                            $sabManager->rename($rempath, 'NonTraite_' . $file);
                        }
                    }
                }
            }
        }
    }
}
