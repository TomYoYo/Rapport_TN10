<?php

namespace Editique\TitreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePortefeuilleCommand extends ContainerAwareCommand
{
    protected $dirSortie;
    protected $dirSortieSab;
    protected $sabManager;
    protected $debug;

    protected function configure()
    {
        $this
            ->setName('editique:generate:portefeuilleTitre')
            ->setDescription('Génére les txt et pdf après être aller chercher les données en BDD.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Si définie, la tache discutera')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init des managers
        $this->portefeuilleManager = $this->getContainer()->get('editique.titres.portefeuilleManager');
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

        $this->portefeuilleManager->setDirSortie($this->dirSortie);
        $this->portefeuilleManager->setEntityManager($em);
        $this->portefeuilleManager->resetRights();
        $this->portefeuilles = $this->portefeuilleManager->getPortefeuilles();
        
        foreach ($this->portefeuilles as $portefeuille) {
            $this->portefeuilleManager->initPortefeuille();
            $this->portefeuilleManager->setData($portefeuille);
            $success = $this->portefeuilleManager->ecrireSortie();
            
            if ($success) {
                $this->countOk++;

                if ($this->debug) {
                    echo 'Pour client '.
                        $this->portefeuilleManager->oPortefeuille->getIdClient().
                        ', et num cpt : '.
                        $this->portefeuilleManager->oPortefeuille->getNumCompte().' : '.
                        'Releve de ' .
                        $this->portefeuilleManager->oPortefeuille->nbPage." page(s) " .
                        " généré avec succès\n";
                }

                // on construit le tableau du pdf global
                if ($this->portefeuilleManager->oPortefeuille->getNbCopie() > 1) {
                    $this->portefeuilleManager->integrerPortefeuillePourImpression();
                }
            } else {
                $this->countKo ++;
            }
        }
        
        // on construit les PDF globaux
        $this->portefeuilleManager->ecrirePDFGlobaux($this->dirSortie);
        if ($this->debug) {
            echo "Fichier Global généré \n";
        }

        if ($this->countOk > 0) {
            $logManager->addSuccess(
                $this->countOk . ' portefeuilles titre générés avec succès.',
                'Editique > Portefeuille Titre',
                'Commande de génération des Portefeuilles titre'
            );
            if ($this->debug) {
                echo $this->countOk . " portefeuilles générés avec succès. \n";
            }
        }
    }
}
