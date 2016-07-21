<?php

namespace Transverse\PartenaireBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Transverse\PartenaireBundle\Entity\MailOI25;
use Transverse\PartenaireBundle\Entity\Oi2504;
use Transverse\PartenaireBundle\Entity\Oi250109;

class CheckAlerteImpayeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('transverse:check:alerteimpaye')
            ->setDescription('Récupération des fichiers OI25.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server  = $this->getContainer()->getParameter('svWin.server');
        $user = $this->getContainer()->getParameter('svWin.user');
        $password =$this->getContainer()->getParameter('svWin.pass');
        $fileRepertory  = $this->getContainer()->getParameter('svWin.dirBanqueFluxCB');
        $OI25BfiRepertory  = $this->getContainer()->getParameter('dirOI25');

        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        $session = ftp_connect($server, 21);

        if (ftp_login($session, $user, $password)) {
            $lm->addSuccess(
                'Connexion sur '.$server.' OK',
                'Transverse > Partenaire > AlerteImpaye',
                'Connexion sur le serveur réussie'
            );
        } else {
            $lm->addError(
                'Erreur de connection sur '.$server.'',
                'Transverse > Partenaire > AlerteImpaye',
                'Echec de connexion sur le serveur'
            );
        }

        //on veut j-1 jour ouvré - du coup il faut sauter les weekends et les jours fériés
        function isNotWorkable($date) {
            if ($date === null) {
                $date = time();
            }

            $date = strtotime(date('m/d/Y',$date));
            $year = date('Y',$date);
            $easterDate  = easter_date($year);
            $easterDay   = date('j', $easterDate);
            $easterMonth = date('n', $easterDate);
            $easterYear   = date('Y', $easterDate);

            $holidays = array(
                // Dates fixes
                mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
                mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
                mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
                mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
                mktime(0, 0, 0, 8,  15, $year),  // Assomption
                mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
                mktime(0, 0, 0, 11, 11, $year),  // Armistice
                mktime(0, 0, 0, 12, 25, $year),  // Noel
                // Dates variables
                mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear),
                mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
                mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
            );

            return in_array($date, $holidays);
        }

        function isWeekend($date) {
            return (date('N', strtotime($date)) >= 6);
        }

        //OI25 tombe tous les matins à 5h15 ac la date de la veille
        $dateDuJour = date('Ymd');
        $dateDuJour = '20160414';
        $hier = date('Ymd',strtotime($dateDuJour . "-1 days"));


        for ($i = 1; ; $i++) {
            if (isNotWorkable(strtotime($hier)) or isWeekend($hier)) {
                $hier = date('Ymd', strtotime($hier . "-1 days"));
            } else {
                break;
            }
            if ($i > 100) {
                break;
            }
            echo $i;
        }

        $regex = '#^'.$hier.'_[0-9]+_OI25$#';
        $contents = ftp_nlist($session, $fileRepertory);
        $matches = preg_grep($regex, $contents);
        $nomFichier = array_shift($matches);

        $em = $this->getContainer()->get('doctrine')->getManager();

        //On récupère les noms des fichiers pour ne pas choper de doublons
         $Oi250109Urls = $em->getRepository('TransversePartenaireBundle:Oi250109')->findAll();

        $urls = array();
        foreach ($Oi250109Urls as $Oi250109Url) {
            $urls[] = $Oi250109Url->getNomFichierURL();
        }
//and !in_array($nomFichier, $urls)  A NE PAS OUBLIER
        if ($nomFichier != null ) {
            //On récup le fichier sur le serveur BFI
            if (ftp_get($session, $OI25BfiRepertory.$nomFichier,  $fileRepertory.$nomFichier, FTP_ASCII)) {

                // on lit le fichier si y a que 2 lignes 01 et 09, on laisse sinon on stock les lignes
                $handle = fopen($OI25BfiRepertory.$nomFichier, "r");
                $cpt = 0;
                $cpt_04 = 0;


                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $cpt++;

                        $o = substr($line, 0, 2);
                        if ($o == '01') {
                            $nomFichier2 = substr($line, 12, 8);
                            $dateJourneeEchange = substr($line, 20, 8);
                            $numeroSequenceFichier = substr($line, 28, 6);
                            $idEtablissementDestinataire = substr($line, 34, 5);

                            $OI25_01 = new Oi250109();
                            $OI25_01->setNomFichierURL($nomFichier);
                            $OI25_01->setNomFichier($nomFichier2);
                            $OI25_01->setDateJourneeEchange($dateJourneeEchange);
                            $OI25_01->setNumeroSequenceFichier($numeroSequenceFichier);
                            $OI25_01->setIdEtablissementDestinataire($idEtablissementDestinataire);

                        } elseif ($o == '04') {
                            $numDossierGESICA[$cpt_04]= substr($line, 6, 12);
                            $typeDossier[$cpt_04] = substr($line, 18, 1);
                            $reseau[$cpt_04] = substr($line, 19, 2);
                            $typeEvenement[$cpt_04] = substr($line, 21, 6);
                            $numCycle[$cpt_04] = substr($line, 27, 1);
                            $caracteristiqueEvenement[$cpt_04] = substr($line, 28, 1);
                            $typeTransaction[$cpt_04] = substr($line, 29, 3);
                            $sensEvenement[$cpt_04] = substr($line, 32, 1);
                            $motifEvenement[$cpt_04] = substr($line, 33, 4);
                            $libelleMotifEvenement[$cpt_04] = substr($line, 37, 50);
                            $numeroIsoCarte[$cpt_04] = substr($line, 87, 19);
                            $nomPrenomPorteur[$cpt_04] = substr($line, 106, 60);
                            $motifOppositionCarte[$cpt_04] = substr($line, 166, 1);
                            $dateOppositionCarte[$cpt_04] = substr($line, 167, 8);
                            $formatCompteImpute[$cpt_04] = substr($line, 175, 3);
                            $compteImpute[$cpt_04] = substr($line, 178, 40);
                            $libelleComptable[$cpt_04] = substr($line, 218, 100);
                            $montantImpute[$cpt_04] = substr($line, 318, 16);
                            $deviseMontantImpute[$cpt_04] = substr($line, 334, 3);
                            $nombreDecimalesDeviseImpute[$cpt_04] = substr($line, 337, 1);
                            $typeMontantImpute[$cpt_04] = substr($line, 338, 1);
                            $datePrelevementVirement[$cpt_04] = substr($line, 339, 8);
                            $sensOperation[$cpt_04] = substr($line, 347, 1);
                            $dateAchat[$cpt_04] = substr($line, 348, 8);
                            $dateCompensation[$cpt_04] = substr($line, 356, 8);
                            $montantAchatBrut[$cpt_04] = substr($line, 364, 16);
                            $deviseMontantAchat[$cpt_04] = substr($line, 380, 3);
                            $nombreDecimalesDeviseAchat[$cpt_04] = substr($line, 383, 1);
                            $montantAchatDeviseOrigine[$cpt_04] = substr($line, 384, 16);
                            $deviseOrigine[$cpt_04] = substr($line, 400, 3);
                            $nombreDecimaleDeviseOrigine[$cpt_04] = substr($line, 403, 1);
                            $montantCompenseEuro[$cpt_04] = substr($line, 404, 16);
                            $deviseCompensation[$cpt_04] = substr($line, 420, 3);
                            $nbDecimalesDeviseCompensation[$cpt_04] = substr($line, 423, 1);
                            $montantCommissionsBanqueInt[$cpt_04] = substr($line, 424, 12);
                            $deviseCommissionsBanque[$cpt_04] = substr($line, 436, 3);
                            $nbDecimalesDeviseCompensation2[$cpt_04] = substr($line, 439, 1);
                            $montantCommissionsInterchange[$cpt_04] = substr($line, 440, 12);
                            $deviseCommissionsBanque2[$cpt_04] = substr($line, 452, 3);
                            $nbDecimalesDeviseCompensation3[$cpt_04] = substr($line, 455, 1);
                            $referenceUnique[$cpt_04] = substr($line, 456, 16);
                            $ARN[$cpt_04] = substr($line, 472, 23);
                            $enseigneCommercant[$cpt_04] = substr($line, 495, 40);
                            $siretCommercant[$cpt_04] = substr($line, 535, 15);
                            $numeroContratCommercant[$cpt_04] = substr($line, 550, 20);
                            $referenceClient[$cpt_04] = substr($line, 570, 20);
                            $RIBTiers[$cpt_04] = substr($line, 590, 3);
                            $RIBTiers2[$cpt_04] = substr($line, 593, 40);

                            $OI25_04[$cpt_04] = new Oi2504();
                            $OI25_04[$cpt_04]->setNumDossierGESICA($numDossierGESICA[$cpt_04]);
                            $OI25_04[$cpt_04]->setTypeDossier($typeDossier[$cpt_04]);
                            $OI25_04[$cpt_04]->setReseau($reseau[$cpt_04]);
                            $OI25_04[$cpt_04]->setTypeEvenement($typeEvenement[$cpt_04]);
                            $OI25_04[$cpt_04]->setNumCycle($numCycle[$cpt_04]);
                            $OI25_04[$cpt_04]->setCaracteristiqueEvenement($caracteristiqueEvenement[$cpt_04]);
                            $OI25_04[$cpt_04]->setTypeTransaction($typeTransaction[$cpt_04]);
                            $OI25_04[$cpt_04]->setSensEvenement($sensEvenement[$cpt_04]);
                            $OI25_04[$cpt_04]->setMotifEvenement($motifEvenement[$cpt_04]);
                            $OI25_04[$cpt_04]->setLibelleMotifEvenement($libelleMotifEvenement[$cpt_04]);
                            $OI25_04[$cpt_04]->setNumeroIsoCarte($numeroIsoCarte[$cpt_04]);
                            $OI25_04[$cpt_04]->setNomPrenomPorteur($nomPrenomPorteur[$cpt_04]);
                            $OI25_04[$cpt_04]->setMotifOppositionCarte($motifOppositionCarte[$cpt_04]);
                            $OI25_04[$cpt_04]->setDateOppositionCarte($dateOppositionCarte[$cpt_04]);
                            $OI25_04[$cpt_04]->setFormatCompteImpute($formatCompteImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setCompteImpute($compteImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setLibelleComptable($libelleComptable[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantImpute($montantImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseMontantImpute($deviseMontantImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setNombreDecimalesDeviseImpute($nombreDecimalesDeviseImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setTypeMontantImpute($typeMontantImpute[$cpt_04]);
                            $OI25_04[$cpt_04]->setDatePrelevementVirement($datePrelevementVirement[$cpt_04]);
                            $OI25_04[$cpt_04]->setSensOperation($sensOperation[$cpt_04]);
                            $OI25_04[$cpt_04]->setDateAchat($dateAchat[$cpt_04]);
                            $OI25_04[$cpt_04]->setDateCompensation($dateCompensation[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantAchatBrut($montantAchatBrut[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseMontantAchat($deviseMontantAchat[$cpt_04]);
                            $OI25_04[$cpt_04]->setNombreDecimalesDeviseAchat($nombreDecimalesDeviseAchat[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantAchatDeviseOrigine($montantAchatDeviseOrigine[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseOrigine($deviseOrigine[$cpt_04]);
                            $OI25_04[$cpt_04]->setNombreDecimaleDeviseOrigine($nombreDecimaleDeviseOrigine[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantCompenseEuro($montantCompenseEuro[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseCompensation($deviseCompensation[$cpt_04]);
                            $OI25_04[$cpt_04]->setNbDecimalesDeviseCompensation($nbDecimalesDeviseCompensation[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantCommissionsBanqueInt($montantCommissionsBanqueInt[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseCommissionsBanque($deviseCommissionsBanque[$cpt_04]);
                            $OI25_04[$cpt_04]->setNbDecimalesDeviseCompensation2($nbDecimalesDeviseCompensation2[$cpt_04]);
                            $OI25_04[$cpt_04]->setMontantCommissionsInterchange($montantCommissionsInterchange[$cpt_04]);
                            $OI25_04[$cpt_04]->setDeviseCommissionsBanque2($deviseCommissionsBanque2[$cpt_04]);
                            $OI25_04[$cpt_04]->setNbDecimalesDeviseCompensation3($nbDecimalesDeviseCompensation3[$cpt_04]);
                            $OI25_04[$cpt_04]->setReferenceUnique($referenceUnique[$cpt_04]);
                            $OI25_04[$cpt_04]->setARN($ARN[$cpt_04]);
                            $OI25_04[$cpt_04]->setEnseigneCommercant($enseigneCommercant[$cpt_04]);
                            $OI25_04[$cpt_04]->setSiretCommercant($siretCommercant[$cpt_04]);
                            $OI25_04[$cpt_04]->setNumeroContratCommercant($numeroContratCommercant[$cpt_04]);
                            $OI25_04[$cpt_04]->setReferenceClient($referenceClient[$cpt_04]);
                            $OI25_04[$cpt_04]->setRIBTiers($RIBTiers[$cpt_04]);
                            $OI25_04[$cpt_04]->setRIBTiers2($RIBTiers2[$cpt_04]);

                            $OI25_01->addOI2504($OI25_04[$cpt_04]);
                            $OI25_04[$cpt_04]->setOi250109($OI25_01);

                            $cpt_04++;

                        }
                    }

                    fclose($handle);
                    //si on a 2 lignes ou moins c'est qu'il n'y a pas d'impayés - on récupère que si y a + de lignes
                    if ($cpt > 2) {
                        $em->persist($OI25_01);
                        $em->flush();

                        $lm->addInfo(
                            'Le fichier OI25 '.$nomFichier.' a été poussé en base ',
                            'Transverse > Partenaire > OI25',
                            'Insertion en base du OI25 '
                        );

                        $idOI25_01 = $OI25_01->getId();
                        $mails = $em->getRepository('TransversePartenaireBundle:MailOI25')->findAll();

                        $Oi25 = $em->getRepository('TransversePartenaireBundle:Oi250109')->findOneById($idOI25_01);

                        //on ajoute les decimales sur les montants

                        $oi2504s = $Oi25->getoI2504s();
                        foreach ($oi2504s as $oi2504) {

                            $montant = $oi2504->getMontantImpute();
                            if ($montant != null) {
                                $decimale = $oi2504->getNombreDecimalesDeviseImpute();
                                if ($decimale > 0) {
                                    $pos = (16 - $decimale);
                                    $montant = substr_replace($montant, ',', $pos, 0);
                                }
                                $var = ltrim($montant, '0');
                                $oi2504->setMontantImpute($var);
                            }
                            $montantAchatBrut =  $oi2504->getMontantAchatBrut();
                            if ($montantAchatBrut != null) {
                                $nombreDecimalesDeviseAchat = $oi2504->getNombreDecimalesDeviseAchat();
                                if ($nombreDecimalesDeviseAchat > 0) {
                                    $pos = (16 - $nombreDecimalesDeviseAchat);
                                    $montantAchatBrut = substr_replace($montantAchatBrut, ',', $pos, 0);
                                }
                                $var = ltrim($montantAchatBrut, '0');
                                $oi2504->setMontantAchatBrut($var);
                            }
                            $montantAchatDeviseOrigine =  $oi2504->getMontantAchatDeviseOrigine();
                            if ($montantAchatDeviseOrigine != null) {
                                $nombreDecimaleDeviseOrigine = $oi2504->getNombreDecimaleDeviseOrigine();
                                if ($nombreDecimaleDeviseOrigine > 0) {
                                    $pos = (16 - $nombreDecimaleDeviseOrigine);
                                    $montantAchatDeviseOrigine = substr_replace($montantAchatDeviseOrigine, ',', $pos, 0);
                                }
                                $var = ltrim($montantAchatDeviseOrigine, '0');
                                $oi2504->setMontantAchatDeviseOrigine($var);
                            }
                            $montantCompenseEuro =  $oi2504->getMontantCompenseEuro();
                            if ($montantCompenseEuro != null) {
                                $nbDecimalesDeviseCompensation = $oi2504->getNbDecimalesDeviseCompensation();
                                if ($nbDecimalesDeviseCompensation > 0) {
                                    $pos = (16 - $nbDecimalesDeviseCompensation);
                                    $montantCompenseEuro = substr_replace($montantCompenseEuro, ',', $pos, 0);
                                }
                                $var = ltrim($montantCompenseEuro, '0');
                                $oi2504->setMontantCompenseEuro($var);
                            }
                            $montantCommissionsBanqueInt =  $oi2504->getMontantCommissionsBanqueInt();
                            if ($montantCommissionsBanqueInt != null) {
                                $nbDecimalesDeviseCompensation2 = $oi2504->getNbDecimalesDeviseCompensation2();
                                if ($nbDecimalesDeviseCompensation2 > 0) {
                                    $pos = (12 - $nbDecimalesDeviseCompensation2);
                                    $montantCommissionsBanqueInt = substr_replace($montantCommissionsBanqueInt, ',', $pos, 0);
                                }
                                $var = ltrim($montantCommissionsBanqueInt, '0');
                                $oi2504->setMontantCommissionsBanqueInt($var);
                            }
                            $montantCommissionsInterchange =  $oi2504->getMontantCommissionsInterchange();
                            if ($montantCommissionsInterchange != null) {
                                $nbDecimalesDeviseCompensation3 = $oi2504->getNbDecimalesDeviseCompensation3();
                                if ($nbDecimalesDeviseCompensation3 > 0) {
                                    $pos = (12 - $nbDecimalesDeviseCompensation3);
                                    $montantCommissionsInterchange = substr_replace($montantCommissionsInterchange, ',', $pos, 0);
                                }
                                $var = ltrim($montantCommissionsInterchange, '0');
                                $oi2504->setMontantCommissionsInterchange($var);
                            }
                        }





                        foreach ($mails as $lemail) {

                            $adressemail = $lemail->getMail();

                            $headers = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                            $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

                            // Envoi du mail
                            mail(
                                $adressemail,
                                'Fichier OI25 - IMP - ' . $nomFichier . '',
                                $this->getContainer()->get('templating')->render(
                                    'TransversePartenaireBundle:AlerteImpaye:Mail/mail_OI25_alert.html.twig',
                                    array(
                                        'nomFichier' => $nomFichier,
                                        'idOI25_01' => $idOI25_01,
                                        'Oi25s' => $Oi25,
                                    )
                                ),
                                $headers
                            );
                        }
                    }
                }

            } else {
                $lm->addError(
                    'Le fichier OI25 '.$nomFichier.' n a pas été récupéré sur le serveur',
                    'Transverse > Partenaire > OI25',
                    'Echec de récupération de fichier'
                );
            }

        }
        ftp_close($session);
    }

}
