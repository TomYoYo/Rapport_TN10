<?php

namespace Editique\TitreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAvisOpereCommand extends ContainerAwareCommand
{
    protected $dirSortie;
    protected $dirSortieSab;
    protected $sabManager;
    protected $debug;

    protected function configure()
    {
        $this
            ->setName('editique:generate:avisOpere')
            ->setDescription('Génére les txt et pdf après être aller chercher les données en BDD.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Si définie, la tache discutera')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init des managers
        $this->avisManager = $this->getContainer()->get('editique.titres.avisManager');
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $this->sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');
        $em = $this->getContainer()->get('doctrine')->getManager('bfi2');
        
        // les repertoires exchange
        $this->dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');
        
        // Init
        $this->countOk = 0;
        $this->countKo = 0;
        $this->debug = $input->getOption('debug');

        $this->avisManager->setDirSortie($this->dirSortie);
        $this->avisManager->setEntityManager($em);
        $this->avis = $this->avisManager->getAvis();
        
        foreach ($this->avis as $avis) {
            $this->avisManager->initAvis();
            $this->avisManager->setData($avis);
            $success = $this->avisManager->ecrireSortie();
            
            if ($success) {
                $this->countOk++;

                if ($this->debug) {
                    echo 'Pour client '.
                        $this->avisManager->oAvis->getIdClient().
                        ', et num cpt : '.
                        $this->avisManager->oAvis->getNumCompte().' : '.
                        'Avis d\'opéré de ' .
                        $this->avisManager->oAvis->nbPage." page(s) " .
                        " généré avec succès\n";
                }

                // on construit le tableau du pdf global
                if ($this->avisManager->oAvis->getNbCopie() > 1) {
                    $this->avisManager->integrerAvisPourImpression();
                }
            } else {
                $this->countKo ++;
            }
        }
        
        // on construit les PDF globaux
        $this->avisManager->ecrirePDFGlobaux($this->dirSortie);
        if ($this->debug) {
            echo "Fichier Global généré \n";
        }

        if ($this->countOk > 0) {
            $logManager->addSuccess(
                $this->countOk . ' avis d\'opéré générés avec succès.',
                'Editique > Avis d\'opéré',
                'Commande de génération des avis d\'opéré'
            );
            if ($this->debug) {
                echo $this->countOk . " avis d\'opéré générés avec succès. \n";
            }
        }
    }
}
