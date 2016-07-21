<?php

namespace Transverse\PartenaireBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Transverse\PartenaireBundle\Entity\PainEnErreur;
use Transverse\PartenaireBundle\Entity\Mail;
use Transverse\PartenaireBundle\Entity\Bic;

class CheckPainsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('transverse:check:pains')
            ->setDescription('Récupération de fichiers Pain.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getContainer()->getParameter('sabCore.user');
        $server = $this->getContainer()->getParameter('sabCore.server');
        $clePrivee = $this->getContainer()->getParameter('sabCore.clePrivee');
        $clePublic = $this->getContainer()->getParameter('sabCore.clePublic');
        //$clePrivee = $this->container->getParameter('sabCore.clePriveeApache');
        //$clePublic = $this->container->getParameter('sabCore.clePublicApache');

        $painRepertory  = $this->getContainer()->getParameter('sabCore.PainFiles');
        $painBfiRepertory  = $this->getContainer()->getParameter('dirPains');

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
                'Transverse > Partenaire > fluxCB',
                'Connexion sur le serveur réussie'
            );
        } else {
            $lm->addError(
                'Erreur de connection sur '.$server.'',
                'Transverse > Partenaire > fluxCB',
                'Echec de connexion sur le serveur'
            );
        }

        $stream = ssh2_exec($session, 'find /.'.$painRepertory.' -maxdepth 1 -name *.xml');
        stream_set_blocking($stream, true);
        $FilesName = stream_get_contents($stream);
        fclose($stream);

        $filesTmp = explode("\n", $FilesName);

        $PainMovedOnBFI = array();

        foreach ($filesTmp as $keyfilesTmp => $valuefilesTmp) {

            if ($valuefilesTmp != null) {
                ssh2_scp_recv($session, $valuefilesTmp, $painBfiRepertory . basename($valuefilesTmp));
                $PainMovedOnBFI[] = basename($valuefilesTmp);
            }
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        //$bics correspond aux BIC stockés dans SAB, c'est la référence qu'on utilise pour vérifier les BICS des fichiers PAIN
        $bics = $em->getRepository('TransversePartenaireBundle:Bic')->findBic();
        $mails = $em->getRepository('TransversePartenaireBundle:Mail')->findAll();
        //On ne garde que la valeur et on nettoie le nom de la clé
        $onlyBic = array_column($bics, 'bic');
        //on supprime les doublons pour gagner de la place sur le tableau
        $onlyBic = array_unique($onlyBic);


        foreach ($PainMovedOnBFI as $key => $lePain) {
            $fichier = $painBfiRepertory.$lePain;
            $xml = simplexml_load_file($fichier);
            //On récupère le BIC pour chaque balises DrctDbtTxInf
            //les balises BIC sont au 6eme niveau $xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->DbtrAgt->FinInstnId->BIC
            //On compte le nombre de balises
            $p_cnt = count($xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf);
            $BicInError = array();

            for ($i = 0; $i < $p_cnt; $i++) {
                $param = $xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf[$i];
                //on vérifie si le BIC ($param->DbtrAgt->FinInstnId->BIC) est dans la base de vérification $bics
                if (!in_array($param->DbtrAgt->FinInstnId->BIC, $onlyBic)) {
                    $BicInError[] = $param->DbtrAgt->FinInstnId->BIC;
                }

            }

            if (!empty($BicInError)) {
                //On déplace le fichier dans le répertoire erreur
                $stream = ssh2_exec($session, 'mv '.$painRepertory.$lePain.' '.$painRepertory.'/ERROR/');
                //On stock l'URL bfi dans la bdd bfi
                $PainEnErreur = new PainEnErreur();
                $PainEnErreur->setUrlPain($lePain);
                $arrayBicInError = json_decode(json_encode($BicInError), true);
                $PainEnErreur->setBicsEnErreur($arrayBicInError);
                $em->persist($PainEnErreur);
                $em->flush();
            }

            $idpain = $PainEnErreur->getId();
            //On envoit un mail pour chaque pain en erreur
            foreach ($mails as $lemail) {

                $adressemail = $lemail->getMail();

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

                // Envoi du mail
                mail(
                    $adressemail,
                    'Attention fichier Pain avec des BICs en anomalies - '.$lePain.'',
                    $this->getContainer()->get('templating')->render(
                        'TransversePartenaireBundle:FluxCB:Mail/mail_fluxcb_alert.html.twig',
                        array(
                            'nompain' => $lePain,
                            'idpain'   => $idpain,
                            'bicerreur' => $arrayBicInError,
                        )
                    ),
                    $headers
                );
            }

            unset( $arrayBicInError );
            unset( $BicInError );
        }
    }
}
