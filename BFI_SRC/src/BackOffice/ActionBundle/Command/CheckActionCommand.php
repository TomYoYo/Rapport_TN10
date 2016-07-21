<?php

namespace BackOffice\ActionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BackOffice\ActionBundle\Entity\Action;
use BackOffice\CleanBundle\Entity\RegleNettoyage;

class CheckActionCommand extends ContainerAwareCommand
{
    private $em;
    private $em2;
    private $lm;

    protected function configure()
    {
        $this
            ->setName('trigger:check:action')
            ->setDescription(
                'Démon de vérification de la table trigger_action ' .
                'et lancement de l\'éxecution pour chaque action trouvée.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $this->lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        while (true) {
            $actions = $this->em->getRepository('BackOfficeActionBundle:Action')->findAllCustom();

            foreach ($actions as $action) {
                $res = false;

                switch ($action->getType()) {
                    case "EDITIQUE":
                        $res = $this->checkActionEditique($action);
                        break;
                    case "OD":
                        $res = $this->checkActionOD($action);
                        break;
                    case "SAB":
                        $res = $this->checkActionSAB($action);
                        break;
                    case "FISCALITE":
                        $res = $this->checkActionFiscalite($action);
                        break;
                    case "NETTOYAGE":
                        $res = $this->checkActionNettoyage($action);
                        break;
                }

                if ($res === 'encours') {
                    $action->setEtat('encours');
                } elseif ($res !== false && $res !== 'kodef') {
                    $this->addTry($action, 'OK');
                } elseif ($action->getNbEssai() >= Action::SEUIL_NB_ESSAI || $res === 'kodef') {
                    $this->addTry($action, 'KO');
                } else {
                    $this->addTry($action, 'attente');
                }

                $this->em->persist($action);
                $this->em->flush();
            }

            sleep(3);
        }
    }

    public function checkActionNettoyage(Action $action)
    {
        $res = false;

        switch ($action->getModule()) {
            case 'REGLE':

                $start = microtime(true);

                $sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');
                $winManager = $this->getContainer()->get('back_office_connexion.windowsFTP');

                $SABDAN = array(
                    'fichiers_externes' => $this->getContainer()->getParameter('sabCore.DAN.fichiers_externes'),
                    'PRT01' => $this->getContainer()->getParameter('sabCore.DAN.PRT01'),
                    'f_interface' => $this->getContainer()->getParameter('sabCore.DAN.f_interface')
                );

                $regle = new RegleNettoyage();
                $regle = $this->em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->find($action->getNumCpt());

                if ($regle === null) {
                    $this->lm->addError(
                        'La règle de nettoyage '.$action->getNumCpt().' n\'existe pas',
                        'BackOffice > Action',
                        'Nettoyage'
                    );
                    $res = 'kodef';
                    break;
                }

                $dan = array();
                if ($regle->getOriginServer() == 'sab') {
                    $managerOrigin = $sabManager;
                    $dan = $SABDAN;
                } else {
                    $msg = 'La règle de nettoyage ' . $action->getNumCpt() .
                        ' est mal configurée : mauvais serveur d\'origine';
                    $this->endNettoyage($regle, $msg, false);
                    $res = 'kodef';
                    break;
                }

                if (!$managerOrigin->checkSousDossier($regle, $dan)) {
                    $msg = 'La règle de nettoyage ' . $action->getNumCpt() .
                        ' est mal configurée : mauvais sous dossier du serveur d\'origine';
                    $this->endNettoyage($regle, $msg, false);
                    $res = 'kodef';
                    break;
                }

                if ($regle->getDestinationServer() == 'sab') {
                    $managerDestination = $sabManager;
                } else {
                    $managerDestination = $winManager;
                }

                list($nbFichiersConcernes, $archivesCrees) = $managerOrigin->clean($regle, $dan);

                // on rappatrie au besoin les targz vers bfi
                if ($regle->getOriginServer() != $regle->getDestinationServer()) {
                    $dossierBFI = '/app/archivage/sab/';
                    if ($regle->getDestinationServer() == 'bfi') {
                        $dossierBFI .= $regle->getOriginDir();
                    } else {
                        $dossierBFI .= 'tmp';
                    }

                    foreach ($archivesCrees as $a) {
                        if (trim($a) == '') {
                            continue;
                        }
                        $localFile = $dossierBFI.'/'.basename($a);
                        echo $a.' vers '.$localFile."\n";
                        if ($managerOrigin->downloadArchive($a, $localFile)) {
                            $managerOrigin->delete($a);
                        }
                    }
                }

                if ($regle->getDestinationServer() == 'win') {
                    $dossierDistant = $this->getContainer()->getParameter('svWin.DA.'.$regle->getOriginDir());
                    foreach (glob('/app/archivage/sab/tmp/*') as $file) {
                        echo $file.' vers '.$dossierDistant.'/'.basename($file)."\n";
                        if ($managerDestination->upload($file, $dossierDistant.'/'.basename($file))) {
                            // il l'upload s'est bien passé on supprime le fichier local
                            unlink($file);
                        }
                    }
                }

                $nbArchivesCrees = count($archivesCrees);

                $end = microtime(true);

                $msg = 'La règle de nettoyage '.$action->getNumCpt().' a été executée avec succès. ';
                $msg .= $nbFichiersConcernes . ' fichiers compressés dans ' . $nbArchivesCrees .
                    ' archives en ' . round(($end-$start)) . ' sec ';
                $this->endNettoyage($regle, $msg);

                $res = true;

                break;
        }

        return $res;
    }

    /**
     * Met à jour la regle et log le tout
     * @param RegleNettoyage $regle
     * @param type $resStr
     * @param type $resBool
     */
    private function endNettoyage(RegleNettoyage $regle, $resStr, $resBool = true)
    {
        $regle->setLastLaunch(new \DateTime);
        $regle->setLastResult($resStr);

        $this->em->persist($regle);
        $this->em->flush();

        $fct = $resBool ? 'addSuccess' : 'addError';
        $this->lm->$fct(
            $resStr,
            'BackOffice > Action',
            'Nettoyage'
        );
    }

    public function checkActionEditique($action)
    {
        $directory = $this->getContainer()->getParameter('dirSortieEditique');
        $this->em2 = $this->getContainer()->get('doctrine')->getManager('bfi2');
        $res = false;
        switch ($action->getModule()) {
            case 'DAT':
                $manager = $this->getContainer()->get('editique.datManager');
                $manager->setEntityManager($this->em2);
                $res = $manager->ecrireSortie($directory, $action->getNumCpt());
                break;
            case 'LIVRET':
                $manager = $this->getContainer()->get('editique.livretManager');
                $manager->setEntityManager($this->em2);
                $res = $manager->ecrireSortie($directory, $action->getNumCpt());
                break;
            case 'LETCHQ':
                $manager = $this->getContainer()->get('editique.lettreManager');
                $manager->setEntityManager($this->em2);
                $manager->setEntityManagerPers($this->em2);
                $res = $manager->prepareLettre($action->getNumCpt(), 'CHQ');
                if ($res) {
                    $manager->ecrireSortie($directory, 'CHQ');
                }
                break;
            case 'RELEVE':
                $this->sendMailTypeReleve();
                $res = true;
                break;
            case "ECHEANCIER":
                $manager = $this->getContainer()->get('editique.echeancierManager');
                $manager->initCompte();
                $manager->typeSpool = 'BDD';
                $manager->lireContent($action->getNumCpt());
                $res = $manager->ecrireSortie($directory);
                break;
            case "MDP":
                $manager = $this->getContainer()->get('editique.compteManager');
                $manager->reinit();
                $manager->initForTrigger($action->getNumCpt());
                $res = $manager->ecrireSortie($directory, 'mdp');
                break;
            case "SOUSCRIPTION_CREDIT":
                $manager = $this->getContainer()->get('editique.souscriptionManager');
                $manager->reinit();
                $manager->setEntityManager2($this->em2);
                $manager->setDatas($action->getNumCpt());
                $res = $manager->ecrireSortie($directory);
                break;
        }

        return $res;
    }

    public function checkActionOD($action)
    {
        $res = false;

        switch ($action->getModule()) {
            case 'CRE':
                $directory = $this->getContainer()->getParameter('dirSortieModuleODCRE');
                $directorySab = $this->getContainer()->getParameter('sabCore.dirSortie2');
                $sabSFTP = $this->getContainer()->get('back_office_connexion.SabSFTP');

                $fileName = "ZXCPTJC0_ODMULTI_".date('Ymd').".dat";
                $fNameSab = "ZXCPTJC0.dat";

                $res = $sabSFTP->upload($directory.$fileName, $directorySab.$fNameSab);
                break;
        }

        return $res;
    }

    public function checkActionSAB($action)
    {
        $res = false;

        switch ($action->getModule()) {
            case "LOGS":
                $scriptSyncLogSAB = $this->getContainer()->getParameter('scriptSyncLogSAB');
                $res = $this->lanceSynchroSAB($scriptSyncLogSAB, $action->getEtat());
                break;
            case "ARBO":
                $scriptSyncArboSAB = $this->getContainer()->getParameter('scriptSyncArboSAB');
                $res = $this->lanceSynchroSAB($scriptSyncArboSAB, $action->getEtat());
                break;
        }

        return $res;
    }

    public function checkActionFiscalite($action)
    {
        $res = false;

        switch ($action->getModule()) {
            case "ETATS":
                $scriptSyncLogSAB = $this->getContainer()->getParameter('scriptSyncEtatsSAB');
                $res = $this->lanceSynchroSAB($scriptSyncLogSAB, $action->getEtat());
                break;
            case "BUDGET":
                // On transfert les fichiers vers le serveur DWM
                $dwmManager = $this->getContainer()->get('back_office_connexion.dwmSFTP');
                $dirLocal = $this->getContainer()->getParameter('dirSortieDivers');
                $dirDepot = $this->getContainer()->getParameter('dwm.dirDepot');
                $nameRealise = $this->getContainer()->getParameter('dwm.nameRea');
                $nameReference = $this->getContainer()->getParameter('dwm.nameRef');

                $dwmManager->upload($dirLocal.date('ymd')."/".$nameReference, $dirDepot.$nameReference);
                $dwmManager->upload($dirLocal.date('ymd')."/".$nameRealise, $dirDepot.$nameRealise);
                
                $res = true;
                break;
        }

        return $res;
    }

    private function lanceSynchroSAB($script, $actionEtat = '')
    {
        if (!file_exists($script)) {
            $this->lm->addError(
                'Le fichier de mise à jour '.$script.' n\'existe pas',
                'BackOffice > Action',
                'Synchro SAB'
            );
            return 'kodef';
        }

        $res = exec("ps -aef | grep '$script'| grep -v grep | wc -l");
        if ($actionEtat == 'attente') {
            if ($res === '0') {
                exec($script.' &');
            }
            $res = 'encours';
        } elseif ($actionEtat == 'encours') {
            if ($res === '0') {
                return true;
            } else {
                return 'encours';
            }
        }
    }

    private function addTry($action, $type)
    {
        $action
            ->setEtat($type)
            ->setNbEssai($action->getNbEssai() + 1)
            ->setDtDernierEssai(new \Datetime());

        switch ($type) {
            case 'KO':
                $this->lm->addError(
                    'Action ' . $action->getId() . ' : KO',
                    'BackOffice > Action',
                    'Commande "Vérification des triggers"'
                );
                break;
            case 'OK':
                $this->lm->addSuccess(
                    'Action ' . $action->getId() . ' : traitée avec réussite',
                    'BackOffice > Action',
                    'Commande "Vérification des triggers"'
                );
                break;
            case 'attente':
                $this->lm->addAlert(
                    'Action ' . $action->getId() . ' : essai non réussi',
                    'BackOffice > Action',
                    'Commande "Vérification des triggers"'
                );
                break;
        }
    }

    private function sendMailTypeReleve()
    {
        // Envoi mail à l'utilisateur
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

        mail(
            $this->getDestMailErreur(),
            "[BFI/Symfony] Changement libelle Releve",
            $this->getContainer()->get('templating')->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                'parts' => array(
                    array(
                        'title' => 'Changement du libellé d\'un Relevé',
                        'content' => $this->getContainer()->get('templating')->render(
                            'BackOfficeMonitoringBundle:Mailing:mail_change_releve.html.twig',
                            array(
                                'libelle' => 'TEST',
                                'id' => 'TEST'
                            )
                        )
                    )
                )
            )),
            $headers
        );
    }

    private function getDestMailErreur()
    {
        $repoUser = $this->em->getRepository('BackOfficeUserBundle:Profil');
        //$users = $repoUser->search(array('role'=>'ROLE_%ADMIN'));
        $users = $repoUser->findByEmail('david.briand-exterieur@fiducial.net');

        $to = array();
        foreach ($users as $u) {
            $to []= $u->getPrenom().' '.$u->getNom() . '<' . $u->getEmail() . '>';
        }

        return implode(',', $to);
    }
}
