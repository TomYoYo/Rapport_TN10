<?php

namespace Editique\CreditBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Editique\MasterBundle\Entity\TauxCredit;

class GetTauxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:get:taux')
            ->setDescription('Récupère les taux sur le site de la Banque de France.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialisation
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $url = "https://www.banque-france.fr/fileadmin/user_upload/banque_de_france/".
            "Economie_et_Statistiques/Changes_et_Taux/page3_quot.csv";
        $nbJours = 7;
        $nbLignesArray = $nbJours + 1;
        
        // Récupération du fichier CSV
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'BFI');
        $content = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        $days_explode = explode(PHP_EOL, $content);
        $days = array_filter($days_explode);
        $nbDays = count($days);
        
        // Récupération des deux dernières lignes
        $ultimateDay = str_getcsv($days[$nbDays - 1], ';');
        for ($i = 2; $i < $nbLignesArray; $i++) {
            $lastDays[] = str_getcsv($days[$nbDays - $i], ';');
        }
        
        $firstNotTraitedDay = str_getcsv($days[$nbDays - $nbLignesArray], ';');
        
        // Gestion des derniers jours
        foreach ($lastDays as $lastDay) {
            $edit = false;
            $tauxCreditPre = $em->getRepository('EditiqueMasterBundle:TauxCredit')
                ->findOneByDateVal($this->stringToDate($lastDay[0]));

            if ($tauxCreditPre) {
                if ($tauxCreditPre->getEonia() == 'ND') {
                    $tauxCreditPre->setEonia(trim($lastDay[1]) == 'ND' ? null : $this->nullIfNeg($lastDay[1]));
                    $edit = true;
                }
                if ($tauxCreditPre->getEurj1m() == 'ND') {
                    $tauxCreditPre->setEurj1m(trim($lastDay[2]) == 'ND' ? null : $this->nullIfNeg($lastDay[2]));
                    $edit = true;
                }
                if ($tauxCreditPre->getEurj3m() == 'ND') {
                    $tauxCreditPre->setEurj3m(trim($lastDay[3]) == 'ND' ? null : $this->nullIfNeg($lastDay[3]));
                    $edit = true;

                    $this->verifMarge($lastDay[3], $lastDay[0]);
                }
                if ($tauxCreditPre->getEurj6m() == 'ND') {
                    $tauxCreditPre->setEurj6m(trim($lastDay[4]) == 'ND' ? null : $this->nullIfNeg($lastDay[4]));
                    $edit = true;
                }
                if ($tauxCreditPre->getEurj9m() == 'ND') {
                    $tauxCreditPre->setEurj9m(trim($lastDay[5]) == 'ND' ? null : $this->nullIfNeg($lastDay[5]));
                    $edit = true;
                }
                if ($tauxCreditPre->getEurj1a() == 'ND') {
                    $tauxCreditPre->setEurj1a(trim($lastDay[6]) == 'ND' ? null : $this->nullIfNeg($lastDay[6]));
                    $edit = true;
                }

                if ($edit) {
                    $tauxCreditPre->setDateEdit(new \DateTime);
                }
            } else {
                $tauxCreditPre = new TauxCredit();
                $tauxCreditPre
                    ->setDateVal($this->getDateTime(trim($lastDay[0])))
                    ->setEonia(trim($lastDay[1]) == 'ND' ? null : $this->nullIfNeg($lastDay[1]))
                    ->setEurj1m(trim($lastDay[2]) == 'ND' ? null : $this->nullIfNeg($lastDay[2]))
                    ->setEurj3m(trim($lastDay[3]) == 'ND' ? null : $this->nullIfNeg($lastDay[3]))
                    ->setEurj6m(trim($lastDay[4]) == 'ND' ? null : $this->nullIfNeg($lastDay[4]))
                    ->setEurj9m(trim($lastDay[5]) == 'ND' ? null : $this->nullIfNeg($lastDay[5]))
                    ->setEurj1a(trim($lastDay[6]) == 'ND' ? null : $this->nullIfNeg($lastDay[6]))
                    ->setDateEnr(new \DateTime)
                ;

                $this->verifMarge($lastDay[3], $lastDay[0]);
            }

            $em->persist($tauxCreditPre);
            $em->flush();
            
            $lastDaysObj[] = $tauxCreditPre;
        }
        
        // Gestion du dernier jour
        $tauxCredit = new TauxCredit();
        $tauxCredit
            ->setDateVal($this->getDateTime(trim($ultimateDay[0])))
            ->setEonia(trim($ultimateDay[1]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[1]))
            ->setEurj1m(trim($ultimateDay[2]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[2]))
            ->setEurj3m(trim($ultimateDay[3]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[3]))
            ->setEurj6m(trim($ultimateDay[4]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[4]))
            ->setEurj9m(trim($ultimateDay[5]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[5]))
            ->setEurj1a(trim($ultimateDay[6]) == 'ND' ? null : $this->nullIfNeg($ultimateDay[6]))
            ->setDateEnr(new \DateTime)
        ;
        
        $em->persist($tauxCredit);
        $em->flush();
        
        $this->verifMarge($ultimateDay[3], $ultimateDay[0]);
        
        // Si on trouve au moins un ND dans le dernier jour non traité, envoie d'un mail
        if (trim($firstNotTraitedDay[1]) == 'ND'
            || trim($firstNotTraitedDay[2]) == 'ND'
            || trim($firstNotTraitedDay[3]) == 'ND'
            || trim($firstNotTraitedDay[4]) == 'ND'
            || trim($firstNotTraitedDay[5]) == 'ND'
            || trim($firstNotTraitedDay[6]) == 'ND'
        ) {
            // On récupère le dernier jour (plus traité) sans ND
            $secondNotTraitedDay = str_getcsv($days[$nbDays - $nbLignesArray - 1], ';');
            
            $tauxCredit2 = new TauxCredit();
            $tauxCredit2
                ->setDateVal($this->getDateTime(trim($firstNotTraitedDay[0])))
                ->setEonia(trim($firstNotTraitedDay[1]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[1]) : $firstNotTraitedDay[1])
                ->setEurj1m(trim($firstNotTraitedDay[2]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[2]) : $firstNotTraitedDay[2])
                ->setEurj3m(trim($firstNotTraitedDay[3]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[3]) : $firstNotTraitedDay[3])
                ->setEurj6m(trim($firstNotTraitedDay[4]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[4]) : $firstNotTraitedDay[4])
                ->setEurj9m(trim($firstNotTraitedDay[5]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[5]) : $firstNotTraitedDay[5])
                ->setEurj1a(trim($firstNotTraitedDay[6]) == 'ND' ? $this->nullIfNeg($secondNotTraitedDay[6]) : $firstNotTraitedDay[6])
                ->setDateEnr(new \DateTime)
            ;

            $em->persist($tauxCredit2);
            $em->flush();
            
            $this->sendMail($firstNotTraitedDay, $secondNotTraitedDay);
        } else {
            $tauxCredit2 = null;
        }
        
        // Création du fichier
        $print = $this->createFileTx($lastDaysObj, $tauxCredit, $tauxCredit2);
        $this->moveFile($print);
    }
    
    public function createFileTx($lastDaysObj, $ultimateDay, $dayNotTreated)
    {
        $content = '';
        
        $content .= $this->getLines($ultimateDay);
        
        foreach ($lastDaysObj as $lastDayObj) {
            $content .= $this->getLines($lastDayObj);
        }
        
        if ($dayNotTreated) {
            $content .= $this->getLines($dayNotTreated);
        }
        
        return $content;
    }
    
    public function getLines($object)
    {
        $content = "";
        
        if ($object->getEonia()) {
            $content .=
                "EUR" .
                "EONIA " .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEonia()) .
                "\r\n"
            ;
        }
        if ($object->getEurj1m()) {
            $content .=
                "EUR" .
                "EURJ1M" .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEurj1m()) .
                "\r\n"
            ;
        }
        if ($object->getEurj3m()) {
            $content .=
                "EUR" .
                "EURJ3M" .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEurj3m()) .
                "\r\n"
            ;
        }
        if ($object->getEurj6m()) {
            $content .=
                "EUR" .
                "EURJ6M" .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEurj6m()) .
                "\r\n"
            ;
        }
        if ($object->getEurj9m()) {
            $content .=
                "EUR" .
                "EURJ9M" .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEurj9m()) .
                "\r\n"
            ;
        }
        if ($object->getEurj1a()) {
            $content .=
                "EUR" .
                "EURJ12" .
                $this->transformDate($object->getDateVal()) .
                $this->transformAmount($object->getEurj1a()) .
                "\r\n"
            ;
        }
        
        return $content;
    }
    
    public function transformDate($datetime)
    {
        return "+".$datetime->format('1ymd');
    }
    
    public function getDateTime($date)
    {
        $dateFormat = substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
        return new \DateTime($dateFormat);
    }
    
    public function transformAmount($amount)
    {
        $amountSab = "";
        
        if (substr($amount, 0, 1) == '-') {
            $amountSab .= '-';
            $amount = substr($amount, 1);
        } else {
            $amountSab .= '+';
        }
        
        $array = explode(',', $amount);
        
        $integerPart = $array[0];
        while (strlen($integerPart) < 5) {
            $integerPart = '0' . $integerPart;
        }
        
        $amountSab .= $integerPart . ',';
        
        $decimalPart = $array[1];
        
        while (strlen($decimalPart) < 9) {
            $decimalPart = $decimalPart . '0';
        }
        
        $amountSab .= $decimalPart;
        
        return $amountSab;
    }
    
    /**
     * Ecriture et déplacement du fichier
     */
    private function moveFile($print)
    {
        $directory = $this->getContainer()->getParameter('dirSortieDivers');
        $directorySab = $this->getContainer()->getParameter('sabCore.dirSortie2');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $sabSFTP = $this->getContainer()->get('back_office_connexion.SabSFTP');
        
        $fileName = "ZTAUC010.dat";
        $dateDir = $directory . '/' . date('ymd') . '/';
        
        if (!file_exists($dateDir)) {
            mkdir($dateDir);
        }

        // Créer le fichier
        $fileManager->ecrireFichier($dateDir, $fileName, $print);
        $sabSFTP->upload($dateDir.$fileName, $directorySab.$fileName);
    }
    
    public function sendMail($tauxJourNonTraite, $tauxRecup)
    {
        $tplManager = $this->getContainer()->get('templating');
        
        // Envoi mail à l'utilisateur
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";
        
        mail(
            $this->getDestMailErreur(),
            "[BANQUE FIDUCIAL] Taux interbancaires",
            $tplManager->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                'parts' => array(
                    array(
                        'title' => 'Récupération des taux',
                        'content' => $tplManager->render(
                            'EditiqueMasterBundle:Mailing:mail_tx_inter.html.twig',
                            array(
                                'tauxJour' => $tauxJourNonTraite,
                                'tauxRecup' => $tauxRecup
                            )
                        )
                    )
                )
            )),
            $headers
        );
    }
    
    public function sendMail2($taux, $date)
    {
        $tplManager = $this->getContainer()->get('templating');
        
        // Envoi mail à l'utilisateur
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";
        
        mail(
            $this->getDestMailErreur(),
            "[BANQUE FIDUCIAL] Taux interbancaires",
            $tplManager->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                'parts' => array(
                    array(
                        'title' => 'Récupération des taux',
                        'content' => $tplManager->render(
                            'EditiqueMasterBundle:Mailing:mail_tx_inter2.html.twig',
                            array(
                                'taux' => $taux,
                                'date' => $date
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
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $repoUser = $em->getRepository('BackOfficeUserBundle:Profil');
        $users = $repoUser->search(array('notification' => 'TAUX'));

        $to = array();
        foreach ($users as $u) {
            $to []= $u->getPrenom().' '.$u->getNom() . '<' . $u->getEmail() . '>';
        }
        
        return implode(',', $to);
    }
    
    private function stringToDate($string)
    {
        $day = substr($string, 0, 2);
        $month = substr($string, 3, 2);
        $year = substr($string, 6, 4);
        
        $formatedDate = $year . '-' . $month . '-' . $day;
        
        return new \DateTime($formatedDate);
    }
    
    private function verifMarge($taux, $date)
    {
        if ($taux  <= -1.5) {
            $this->sendMail2($taux, $date);
        }
    }

    private function nullIfNeg($taux)
    {
        if (substr($taux, 0, 1) == '-') {
            return '0,000000001';
        }

        return $taux;
    }
}
