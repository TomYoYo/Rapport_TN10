<?php
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 28/04/2016
 * Time: 09:43
 */

namespace BackOffice\CustomerBundle\Command;

use BackOffice\CustomerBundle\Entity\Anomalie;
use BackOffice\CustomerBundle\Entity\Customer;
use BackOffice\CustomerBundle\Entity\SettingsCivility;
use BackOffice\CustomerBundle\Entity\SettingsStateCode;
use BackOffice\CustomerBundle\Manager\InformationsManager;
use BackOffice\MonitoringBundle\Manager\LogManager;
use Doctrine\ORM\EntityManager;
use mageekguy\atoum\asserters\string;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportResetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitoring:customer:init')
            ->setDescription('Import et traitement des clients Fidinfo ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $utf8 = array(
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u' => 'A',
            '/[ÍÌÎÏ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/–/' => '-', // conversion d'un tiret UTF-8 en un tiret simple
            '/[‘’‚‹›]/u' => ' ', // guillemet simple
            '/[“”«»„]/u' => ' ', // guillemet double
            '/ /' => ' ', // espace insécable (équiv. à 0x160)
            '/¿/' => '',
            "/'/"=> ' ',
        );
        $path = $this->getContainer()->getParameter('dirEntreeFIDINF');
        $files = scandir($path,1);
        $date = date('Y-m-d');
        /**
         * @var InformationsManager $manager
         */
        /**
         * @var EntityManager $em
         */
        $manager = $this->getContainer()->get('backoffice.informationsmanager');
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->query('TRUNCATE TABLE customer');
        $em->getConnection()->query('TRUNCATE TABLE anomalie');
        setlocale(LC_CTYPE, 'fr_FR.UTF-8');

        if(sizeof($files) > 2)
        {
            for($i=0;$i<sizeof($files)-2;$i++) {
                if (strlen($files[$i]) == 37 && strpos($files[$i], $date . '.csv')) {
                    $row = 0;
                    $new_customer=0;
                    $transco_code_resp = 0;
                    $transco_civility=0;
                    $transco_state = 0;
                    $transco_juridique = 0;
                    $transco_qualite = 0;
                    $transco_category = 0;
                    $libellé = "Debut de l'import des clients externes dans BFI";
                    $output->writeln($libellé);
                    $anomalies = array();
                    $logManager->addInfo($libellé,"BackOffice > Intégration des clients externes","Import données");
                    if(($handle = fopen($path.$files[$i],"r")) !== false)
                    {
                        while (($data = fgetcsv($handle, 0, "|",'*')) !== FALSE) {
                            /**
                             * @var Customer $customer
                             */
                            /**
                             * @var LogManager $logManager
                             */
                            $customer = $em->getRepository('BackOfficeCustomerBundle:Customer')->findOneBy(array('idCustomer'=>$data[0]));
                            if(!$customer)
                            {
                                if ($data[8] != '' && strlen($data[8]) == 9)
                                {
                                    $customer = $manager->checkSiret((string)$data[8].(string)$data[9]);
                                }
                                if(!$customer)
                                {
                                    $customer = new Customer();
                                    $customer->setidCustomer($data[0]);
                                    $new_customer++;
                                    if ($data[2] != '')
                                    {
                                        if(mb_detect_encoding($data[2]) == 'UTF-8')
                                        {
                                            $designation =  str_replace("/"," ",$this->wd_remove_accents(iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[2])));
                                        }
                                        else
                                        {
                                            $designation =  str_replace("/"," ",$this->wd_remove_accents($data[2]));
                                        }
                                        if(!$designation)
                                        {
                                            $designation =  str_replace("/"," ",$this->wd_remove_accents(iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[2])));
                                            if(!$designation)
                                            {
                                                $logManager->addError("Impossible de formater la désignation : ".$data[2]."id : ".$data[0],"BackOffice > Intégration des clients externes","Import données");
                                                continue;
                                            }
                                        }
                                        $customer->setdesignation($designation);
                                    }
                                    if ($data[8] != '' )
                                    {
                                        if(strlen(str_replace("-","",$data[8])) == 9 && is_numeric($data[8]))
                                        {
                                            $customer->setSiren($data[8]);
                                        }
                                        else
                                        {
                                            $logManager->addInfo('Numéro de Siret Invalide ID: '.$data[0],"BackOffice > Intégration des clients externes","Import données");
                                            $anomalie = new Anomalie();
                                            $anomalie->setIdCustomer($data[0]);
                                            $anomalie->setstatut(3);
                                            $anomalie->setDesignation($designation);
                                            $anomalie->setcause('Numéro siret invalide '.$data[8].(string)$data[9]);
                                            $new_customer--;
                                            $row++;
                                            $em->persist($anomalie);
                                            $customer = null;
                                            continue;
                                        }
                                    }
                                    if ($data[9] != '' )
                                    {
                                        if(strlen($data[9]) == 5 && is_numeric($data[9]))
                                        {
                                            $customer->setcodeNic((string)$data[9]);
                                        }
                                        else
                                        {
                                            $logManager->addInfo('Numéro de Siret Invalide ID: '.$data[0],"BackOffice > Intégration des clients externes","Import données");
                                            $anomalie = new Anomalie();
                                            $anomalie->setIdCustomer($data[0]);
                                            $anomalie->setstatut(3);
                                            $anomalie->setDesignation($designation);
                                            $anomalie->setcause('Numéro siret invalide '.$data[8].(string)$data[9]);
                                            $new_customer--;
                                            $row++;
                                            $em->persist($anomalie);
                                            $customer = null;

                                            continue;
                                        }
                                    }
                                    if ($data[5] != '')
                                    {
                                        $customer->setcodeApe(substr($data[5],0,5));
                                    }
                                    if ($data[28] != '')
                                        $customer->setcapital($data[28]);
                                    if ($data[20] != '') {
                                        if($data[21] != '')
                                        {
                                            if($data[22] != '')
                                            {
                                                $addr = '';
                                                if(mb_detect_encoding($data[20]) == 'UTF-8')
                                                {
                                                    $addr .= iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[20]).' ';
                                                }
                                                else
                                                {
                                                    $addr .= $data[20].' ';
                                                }
                                                if(mb_detect_encoding($data[21]) == 'UTF-8')
                                                {
                                                    $addr .= iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[21]).' ';
                                                }
                                                else
                                                {
                                                    $addr .= $data[21].' ';
                                                }
                                                if(mb_detect_encoding($data[22]) == 'UTF-8')
                                                {
                                                    $addr .= iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[22]);
                                                }
                                                else
                                                {
                                                    $addr .= $data[22];
                                                }
                                                $customer->setadresse(preg_replace(array_keys($utf8), array_values($utf8),$addr));
                                            }
                                            else
                                            {
                                                $addr = '';
                                                if(mb_detect_encoding($data[20]) == 'UTF-8')
                                                {
                                                    $addr .= iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[20]).' ';
                                                }
                                                else
                                                {
                                                    $addr .= $data[20].' ';
                                                }
                                                if(mb_detect_encoding($data[21]) == 'UTF-8')
                                                {
                                                    $addr .= iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[21]);
                                                }
                                                else
                                                {
                                                    $addr .= $data[21];
                                                }
                                                $customer->setadresse(preg_replace(array_keys($utf8), array_values($utf8),$addr));
                                            }
                                        }else
                                        {
                                            if(mb_detect_encoding($data[20]) == 'UTF-8')
                                            {
                                                $addr = iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[20]);
                                            }
                                            else
                                            {
                                                $addr = $data[20];
                                            }
                                            $customer->setadresse(preg_replace(array_keys($utf8), array_values($utf8),$addr));
                                        }
                                    }
                                    if ($data[16] != '')
                                        $customer->settel($data[16]);
                                    if ($data[17] != '' && strlen(str_replace("-","",$data[17]))==10)
                                        $customer->setfax($data[17]);
                                    if ($data[18] != '')
                                    {
                                        if(mb_detect_encoding($data[18]) == 'UTF-8') {
                                            $customer->setemail(iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[18]));
                                        }
                                        else
                                        {
                                            $customer->setemail($data[18]);
                                        }
                                    }
                                    if ($data[23] != '')
                                        $customer->setcP(trim($data[23]));
                                    if ($data[24] != '')
                                    {
                                        if(mb_detect_encoding($data[24]) == 'UTF-8') {
                                            $customer->setville(iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[24]));
                                        }
                                        else
                                        {
                                            $customer->setville($data[24]);

                                        }
                                    }
                                    if($data[25] != '')
                                        $customer->setpays($data[25]);
                                    if($data[3] != '')
                                        $customer->setdateCreation(new \DateTime(substr($data[3],0,23)));
                                    if($data[4] != ''){
                                        $resp = $manager->getResponsable($data[4]);
                                        if($resp != null)
                                        {
                                            $customer->setresponsable($resp);
                                            $transco_code_resp++;
                                        }
                                        else
                                        {
                                            $logManager->addInfo('Problème de transcodage code responsable ID: '.$data[0].' dept: '.$data[4],"BackOffice > Intégration des clients externes","Import données");
                                            $anomalie = new Anomalie();
                                            $anomalie->setIdCustomer($data[0]);
                                            $anomalie->setstatut(4);
                                            $anomalie->setDesignation(preg_replace(array_keys($utf8), array_values($utf8), $data[2]));
                                            $anomalie->setcause("Transcodage impossible code responsable");
                                            $customer = null;
                                            $em->persist($anomalie);
                                            $row++;
                                            continue;
                                        }
                                    }
                                    if($data[1] != '')
                                    {
                                        if(mb_detect_encoding($data[1]) == 'UTF-8')
                                        {
                                            $customer->setformeJuridiqueExt(iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $data[1]));
                                        }
                                        else
                                        {
                                            $customer->setformeJuridiqueExt($data[1]);
                                        }
                                        /**
                                         * @var SettingsCivility $civilityCode
                                         */
                                        /**
                                         * @var SettingsStateCode $stateCode
                                         */
                                        $civilityCode = $manager->getCivilityCode($customer->getformeJuridiqueExt());
                                        $stateCode = $manager->getStateCode($customer->getformeJuridiqueExt());
                                        $quality = $manager->getQuality($customer->getformeJuridiqueExt());
                                        if(!$quality)
                                        {
                                            $logManager->addInfo('Problème de transcodage code qualité ID: '.$data[0].' forme juridique: '.$data[1],"BackOffice > Intégration des clients externes","Import données");
                                        }
                                        else
                                        {
                                            $customer->setqualiteClient($quality);
                                            $transco_qualite++;
                                        }
                                        if(!$civilityCode)
                                        {
                                            $logManager->addInfo('Problème de transcodage code civilité ID: '.$data[0].' forme juridique: '.$data[1],"BackOffice > Intégration des clients externes","Import données");
                                        }
                                        else
                                        {
                                            $customer->setcodeCivilite($civilityCode);
                                            $transco_civility++;
                                        }
                                        if(!$stateCode)
                                        {
                                            $logManager->addInfo('Problème de transcodage code état: '.$data[0].' forme juridique: '.$data[1],"BackOffice > Intégration des clients externes","Import données");
                                        }
                                        else
                                        {
                                            $customer->setcodeEtat($stateCode);
                                            $transco_state ++;
                                        }
                                    }
                                    $customer->setstatut(1);
                                    $em->persist($customer);

                                }
                                else
                                {
                                    if(!in_array($customer->getidCustomer(),$anomalies))
                                    {
                                        array_push($anomalies,$customer->getidCustomer());
                                    }
                                    $anomalie1 = new Anomalie();
                                    $anomalie1->setIdCustomer($data[0]);
                                    $anomalie1->setDesignation($data[2]);
                                    $anomalie1->setcause('Duplication Numéro Siret : '.$customer->getSiren().'/'.$customer->getcodeNic());
                                    $anomalie2 = new Anomalie();
                                    $anomalie1->setstatut(2);
                                    $anomalie2->setstatut(2);
                                    $anomalie2->setIdCustomer($customer->getidCustomer());
                                    $anomalie2->setDesignation($customer->getdesignation());
                                    $anomalie2->setcause('Duplication Numéro Siret : '.$customer->getSiren().'/'.$customer->getcodeNic());
                                    $em->persist($anomalie1);
                                    $em->persist($anomalie2);
                                    $logManager->addInfo("Anomalie : duplication N°Siret ".$customer->getSiren().'/'.$customer->getcodeNic(),"BackOffice > Intégration des clients externes","Import données");
                                }
                            }
                            else
                            {
                                if(!in_array($customer->getidCustomer(),$anomalies))
                                {
                                    array_push($anomalies,$customer->getidCustomer());
                                }
                                $anomalie1 = new Anomalie();
                                $anomalie1->setIdCustomer($data[0]);
                                $anomalie1->setDesignation($data[2]);
                                $anomalie1->setcause('Duplication ID : '.$data[0]);
                                $anomalie2 = new Anomalie();
                                $anomalie1->setstatut(1);
                                $anomalie2->setstatut(1);
                                $anomalie2->setIdCustomer($customer->getidCustomer());
                                $anomalie2->setDesignation($customer->getdesignation());
                                $anomalie2->setcause('Duplication ID : '.$customer->getidCustomer());
                                $em->persist($anomalie1);
                                $em->persist($anomalie2);
                                $logManager->addInfo("Anomalie : duplication ID ".$data[0],"BackOffice > Intégration des clients externes","Import données");
                            }
                            $row++;
                            if($row%100 == 0)
                            {
                                $output->writeln($row);
                            }
                            $em->flush();
                        }
                        foreach($anomalies as $row_customer)
                        {
                            $em->remove( $customer = $em->getRepository('BackOfficeCustomerBundle:Customer')->findOneBy(array('idCustomer'=>$row_customer)));
                            $em->flush();
                            $new_customer--;
                        }
                        $output->writeln("$new_customer nouveaux client importes sur $row lignes");
                        $output->writeln("$transco_civility transcodages code civilite reussis ");
                        $output->writeln("$transco_code_resp transcodages code responsable reussis ");
                        $output->writeln("$transco_state transcodages code etat reussis ");
                        $output->writeln("$transco_juridique transcodages code nace reussis ");
                        $output->writeln("$transco_qualite transcodages qualité client reussis ");
                        $output->writeln("$transco_category transcodages categorie client reussis ");
                        fclose($handle);

                    }
                }
                else
                {
                    $libellé = "Nom incorrect fichier de données clients ce jour";
                    $output->writeln($libellé);
                    $logManager->addError($libellé,"BackOffice > Intégration des clients externes","Import données");
                }
            }

        }
        else
        {
            $libellé = "Pas de fichier de données clients ce jour";
            $output->writeln($libellé);
            $logManager->addError($libellé,"BackOffice > Intégration des clients externes","Import données");
        }
        $output->writeln('Fin commande');
    }

    function wd_remove_accents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = str_replace("'"," ",$str);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }
}
