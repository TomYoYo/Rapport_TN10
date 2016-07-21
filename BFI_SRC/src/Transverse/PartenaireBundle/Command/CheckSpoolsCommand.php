<?php

namespace Transverse\PartenaireBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Transverse\PartenaireBundle\Entity\Parametrage;
use Transverse\PartenaireBundle\Entity\Spool;

class CheckSpoolsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('transverse:check:spools')
            ->setDescription('Récupération de spools.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getContainer()->getParameter('sabCore.user');
        $server = $this->getContainer()->getParameter('sabCore.server');
        $clePrivee = $this->getContainer()->getParameter('sabCore.clePrivee');
        $clePublic = $this->getContainer()->getParameter('sabCore.clePublic');
        //$clePrivee = $this->getContainer()->getParameter('sabCore.clePriveeApache');
        //$clePublic = $this->getContainer()->getParameter('sabCore.clePublicApache');
        $dirPathPrintTop  = $this->getContainer()->getParameter('sabCore.dirPathPrintTop');
        $spoolRepertory  = $this->getContainer()->getParameter('dirSpools');
        
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        $session  = ssh2_connect($server, 22, array('hostkey'=>'ssh-rsa,ssh-dss'));
        
        if (ssh2_auth_pubkey_file(
            $session,
            $user,
            "/.".$clePublic,
            "/.".$clePrivee,
            null
        )) {
            $lm->addSuccess(
                'Connexion sur '.$server.' OK',
                'Transverse > Partenaire',
                'Connexion sur le serveur réussie'
            );
        } else {
            $lm->addError(
                'Erreur de connection sur '.$server.'',
                'Transverse > Partenaire',
                'Echec de connexion sur le serveur'
            );
        }
        
   
           
        $dateDuJour = (date('Y') >= 2000? 1 : 0) . date('ymd');
        //$dateDuJour = '1160308';


        //on se connecte sur le serveur et on chope les spools qui terminent par .txt et qui datent d'aujourd'hui
        //$stream = ssh2_exec($session, 'ls /.'.$dirPathPrintTop.'*'.$dateDuJour.'*.txt | xargs -n 1 basename');
        $stream = ssh2_exec($session, 'find /.'.$dirPathPrintTop.' -name *_'.$dateDuJour.'*.txt');
        
        stream_set_blocking($stream, true);
        

        
        $AllLinuxFiles = stream_get_contents($stream);
        fclose($stream);
       
        $filesTmp = explode("\n", $AllLinuxFiles);
 
        $em = $this->getContainer()->get('doctrine')->getManager();
        //on chope nos parametrages
        $parametrages = $em->getRepository('TransversePartenaireBundle:Parametrage')->findAll();

        //on chope nos spools du jour pour ne pas récupérer des doublons
        $spools = $em->getRepository('TransversePartenaireBundle:Spool')->findBy(array('dateSpool' => $dateDuJour ));
        $SpoolOwned = array();
        foreach ($spools as $spool) {
            $SpoolName = $spool->getUrlSpool();
            $SpoolOwned[] = basename($SpoolName);
        }
        
        //on enlève les spools qu'on possède déjà des spools du jour qu'on a récup sur le serveur
        foreach ($filesTmp as $keyfilesTmp => $valuefilesTmp) {
            if (in_array(basename($valuefilesTmp), $SpoolOwned)) {
                unset($filesTmp[$keyfilesTmp]);
            }
        }

        $files = $filesTmp;
        
        $FilesQuOnVaMettreSurNotreServeur = array();

        ini_set('max_execution_time', 300);
        foreach ($files as $file) {

            $destroyed = explode("_", basename($file));

            //Pour chaque parametrage on vérifie si le prefixe correspond
            foreach ($parametrages as $parametrage) {
                $prefixeParam = $parametrage->GetPrefixeSpool();
                if ($destroyed[0] == $prefixeParam) {
                    //Le prefixe correspond, dc si on a pas de filtre on récupère le fichier
                    $filtres = $parametrage->GetFiltres();
                    
                    //on vérifie si on récupère les spools en fonction du filtre ou pas
                    $isFiltreIncluded = $parametrage->getIsFiltreIncluded();

                    if ($filtres->isEmpty()) {
                        //pour éviter les doublons
                        if (!in_array($file, $FilesQuOnVaMettreSurNotreServeur)) {
                            $FilesQuOnVaMettreSurNotreServeur[] = $file;
                            $LeSpoolQuOnVaPousser = $file;
                        }

                    } else {
                        //Pr Chaque filtre on va lire le fichier et voir si les filtres apparaissent dedans
                        foreach ($filtres as $filtre) {
                            
                            $lefiltre = $filtre->getExpressionAFiltrer();

                            if ($isFiltreIncluded == false) {
                                
                                //ssh2_exec change l'encodage de $filtre
                                //on est obligé de convertir avec mb_convert_encoding
                                $stream = ssh2_exec(
                                    $session,
                                    'if grep -Fq "' . mb_convert_encoding($lefiltre, mb_internal_encoding(), 'UTF-8') .
                                    '" ' . $file . ';then echo true;else echo false;fi;'
                                );
                            } elseif ($isFiltreIncluded == true) {
                                 $stream = ssh2_exec(
                                     $session,
                                     'if grep -Fq "' . mb_convert_encoding($lefiltre, mb_internal_encoding(), 'UTF-8') .
                                     '" ' . $file . ';then echo false;else echo true;fi;'
                                 );
                            }
                            stream_set_blocking($stream, true);
                            $contenu = stream_get_contents($stream);
                            
                            if (trim($contenu) == 'true') {
                                //pour éviter les doublons
                                if (!in_array($file, $FilesQuOnVaMettreSurNotreServeur)) {
                                    $FilesQuOnVaMettreSurNotreServeur[] = $file;
                                    $LeSpoolQuOnVaPousser = $file;
                                }
                            }
                            fclose($stream);
                        }
                    }//fin si filtre vide ou pas
                    
                    
                    //On récupère le fichier si il est validé
                    if (isset($LeSpoolQuOnVaPousser) and $LeSpoolQuOnVaPousser != null) {
 
                        ssh2_scp_recv($session, $LeSpoolQuOnVaPousser, $spoolRepertory.basename($LeSpoolQuOnVaPousser));
                        //On insère le spool dans la bdd
                        $spool1 = new Spool();
                        $spool1->setUrlSpool($spoolRepertory.basename($LeSpoolQuOnVaPousser));
                        $spool1->setNomSpool(basename($LeSpoolQuOnVaPousser));
                        $spool1->setDateSpool($dateDuJour);
                        $parametrage->addSpool($spool1);
                       
                        $em->persist($parametrage);
                        $em->flush();
                        
                        $lm->addInfo(
                            'Le Spool a été poussé en base ' . basename($LeSpoolQuOnVaPousser),
                            'Transverse > Partenaire',
                            'Insertion en base du spool '
                        );
                        
                        $idspool = $spool1->getId();
                        $titrespool =  $parametrage->getTitreParam();
                        $titrespool = utf8_decode($titrespool);
                        $titrespool = mb_encode_mimeheader($titrespool, "UTF-8");
                        
                        //On envoit un mail pour toutes les personnes concernées
                        
                        $mails = $parametrage->GetTags();
                        $prioriteTmp = $parametrage->getPriorite();
                        
                        if ($prioriteTmp == 0) {
                            $priorite = 'Faible';
                        } elseif ($prioriteTmp == 1) {
                            $priorite = 'Normale';
                        } else {
                            $priorite = 'Haute';
                        }
                           
                        foreach ($mails as $lemail) {

                            $adressemail = $lemail->getMailTag();
                            
                            $headers  = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                            $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";
                            
                            // Envoi du mail
                            mail(
                                $adressemail,
                                'Alerte nouveau spool - '.$titrespool.'',
                                $this->getContainer()->get('templating')->render(
                                    'TransversePartenaireBundle:Spoolalert:Mail/mail_spool_alert.html.twig',
                                    array(
                                        'nomspool' => basename($LeSpoolQuOnVaPousser),
                                        'idspool'   => $idspool,
                                    )
                                ),
                                $headers
                            );
                        }
                        unset($LeSpoolQuOnVaPousser);
                    }
                }
            } //fin foreach parametrages

        }//fin foreach files
        $em->clear();

        //On supprime les arrays pour ne pas charger la mémoire
        unset( $FilesQuOnVaMettreSurNotreServeur );
    }
}
