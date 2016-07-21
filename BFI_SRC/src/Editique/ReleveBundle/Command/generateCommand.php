<?php

namespace Editique\releveBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected $dirSortie;
    protected $dirSortieSab;
    protected $sabManager;
    protected $debug;

    protected function configure()
    {
        $this
            ->setName('editique:generate:releve')
            ->setDescription('Génére les txt et pdf après être aller chercher le fichiers source sur sabCore.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Si définie, la tache discutera')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init des managers
        $this->releveManager = $this->getContainer()->get('editique.releveManager');
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $this->sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');

        // les repertoires exchange
        $dirEntree = $this->getContainer()->getParameter('dirEntreeEditique');
        $this->dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');

        // les repertoires sur sabcore
        $dirRemote = $this->getContainer()->getParameter('sabCore.dirPathPrint');
        $this->dirSortieSab = $this->getContainer()->getParameter('sabCore.dirSortie');

        // Init
        $this->countOk = 0;
        $this->debug = $input->getOption('debug');

        $this->releveManager->setDegradee(false);
        $this->releveManager->setDirSortie($this->dirSortie);

        // les releves correspondent aux fichiers CPT051P1_
        $files = $this->sabManager->fichiersMasques($dirRemote, "/^CPT051P1_\w+\.txt$/");
        //$files = array('CPT_test.txt');
        foreach ($files as $f) {
            // On initialise le oReleve
            $this->releveManager->initOReleve();

            // le count ko est par fichier
            $this->countKo = 0;

            // on copie dans in
            $locpath = $dirEntree . $f;
            $rempath = $dirRemote . $f;
            $this->sabManager->download($rempath, $locpath);

            if ($this->debug) {
                echo 'Traitement du fichier '.$locpath."\n";
            }

            // on lit le fichier et on parcourt releve par releve
            $tabRelevesContent = $this->releveManager->getRelevesFromFile($locpath);
            foreach ($tabRelevesContent as $releveContent) {
                if (trim($releveContent) != '') {
                    $this->releveManager->setContent($releveContent);

                    // si on est sur un nouveau releve
                    if ($this->releveManager->getNumFeuillet() == 1) {
                        // s'il y avait deja un releve en cours
                        if ($this->releveManager->getOReleve() != null) {
                            $this->genererFichier();
                        }

                        // on initialise le releve
                        $this->releveManager->initReleve();
                    }

                    // on lit le contenu du releve
                    $this->releveManager->lireContent();
                }
            }

            // s'il y avait deja un releve en cours
            if ($this->releveManager->getOReleve() != null) {
                $this->genererFichier();
            }

            // une fois tous les releves traites, on passe au bilan
            if ($this->countKo == 0) {
                // si ok on copie le fichier dans treated
                $fileManager->deplacerFichier($dirEntree . $f, $dirTreated . $f);
            } else {
                // si ko on copie le fichier dans error
                $logManager->addAlert(
                    'Relevé ' . $f . ' comporte des erreurs ; déplacé dans le dossier "error".',
                    'Editique > Relevé',
                    'Commande de génération des Relevés'
                );
                $fileManager->deplacerFichier($dirEntree . $f, $dirError . $f);
            }

            // deplacer le fichier dans /A sur sabcore
            $this->sabManager->mv($rempath, $dirRemote . '../A/');
        }

        // on construit les PDF globaux
        $this->releveManager->ecrirePDFGlobaux($this->dirSortie);
        if ($this->debug) {
            echo "Fichier Global généré \n";
        }

        if ($this->countOk > 0) {
            $logManager->addSuccess(
                $this->countOk . ' relevés générés avec succès.',
                'Editique > Relevé',
                'Commande de génération des Relevés'
            );
            if ($this->debug) {
                echo $this->countOk . " relevés générés avec succès. \n";
            }
        }
    }

    public function genererFichier()
    {
        // Generation des fichiers de sortie
        $success = $this->releveManager->ecrireSortie();

        if ($success) {
            $this->countOk++;

            if ($this->debug) {
                echo 'Pour client '.
                    $this->releveManager->oReleve->getIdClient().
                    ', et num cpt : '.
                    $this->releveManager->oReleve->getNumCompte().' : '.
                    'Releve de ' .
                    $this->releveManager->oReleve->nbPage." page(s) " .
                    " généré avec succès\n";
            }

            // on construit le tableau du pdf global
            if ($this->releveManager->getOReleve()->getModeDiffusion() == 'I'
                || $this->releveManager->getOReleve()->getModeDiffusion() == 'G'
            ) {
                $this->releveManager->integrerRelevePourImpression(
                    $this->releveManager->getOReleve()->getModeDiffusion()
                );
            }

            if ($this->releveManager->oReleve->getModeDiffusion() != 'F') {
                // nouveau nom pour esab
                $numCpt = $this->releveManager->oReleve->getNumCompte();
                $newName = date('ymd') . 'RM' . $numCpt . '.pdf';

                // nom actuel
                $pdf = $this->releveManager->getFilename('pdf');

                // upload vers sab
                $this->sabManager->upload($this->dirSortie . $pdf, $this->dirSortieSab . $newName);
            }
        } else {
            $this->countKo ++;
        }
    }
}
