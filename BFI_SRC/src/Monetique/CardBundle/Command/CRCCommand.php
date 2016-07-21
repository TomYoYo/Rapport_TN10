<?php

namespace Monetique\CardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CRCCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monetique:generate:crc')
            ->setDescription('Génére le fichier CRC à destination de la Banque de France.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initVars();
        
        $this->printDTD();
        $this->printAll();
        
        $this->makeFile();
    }
    
    private function initVars()
    {
        $this->print = "";
        $this->records = $this->getRecords();
    }
    
    private function getRecords()
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        
        $query1 = "SELECT GUICB2CL1, SUBSTR(GUICB1SI2, 190, 2) AS PAYS, SUM(GUICB2MON) AS MTT, CLIENASRN "
            . "FROM ZGUICB10, ZGUICB20, ZCLIENA0 "
            . "WHERE ZGUICB10.GUICB1DOS = ZGUICB20.GUICB2DOS "
            . "AND ZGUICB20.GUICB2CL1 = ZCLIENA0.CLIENACLI "
            . "AND ZGUICB20.GUICB2DTR LIKE '" . date('1ym', mktime(12, 0, 0, date("m"), 0, date("Y"))) . "%' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 857, 1) = 'E' "
            . "AND (SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '1' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '2' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '3' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '4' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '5' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '6' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '7' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '8' "
            . "OR SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) = '9') "
            . "AND SUBSTR(ZGUICB10.GUICB1SI1, 9, 3) = 100 "
            . "HAVING SUM(GUICB2MON) > 1000 "
            . "GROUP BY GUICB2CL1, SUBSTR(GUICB1SI2, 190, 2), CLIENASRN"
            ;
        
        $query2 = "SELECT GUICB2CL1, SUBSTR(GUICB1SI2, 190, 2) AS PAYS, SUM(GUICB2MON) AS MTT, CLIENASRN "
            . "FROM ZGUICB10, ZGUICB20, ZCLIENA0 "
            . "WHERE ZGUICB10.GUICB1DOS = ZGUICB20.GUICB2DOS "
            . "AND ZGUICB20.GUICB2CL1 = ZCLIENA0.CLIENACLI "
            . "AND ZGUICB20.GUICB2DTR LIKE '" . date('1ym', mktime(12, 0, 0, date("m"), 0, date("Y"))) . "%' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 857, 1) = 'E' "
            . "AND ((SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '1' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '2' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '3' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '4' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '5' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '6' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '7' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '8' "
            . "AND SUBSTR(ZGUICB10.GUICB1SI2, 906, 1) != '9') "
            . "OR SUBSTR(ZGUICB10.GUICB1SI1, 9, 3) != 100) "
            . "HAVING SUM(GUICB2MON) > 1000 "
            . "GROUP BY GUICB2CL1, SUBSTR(GUICB1SI2, 190, 2), CLIENASRN"
            ;

        $stmt = $em->getConnection()->prepare($query1);
        $stmt->execute();
        $res = $stmt->fetchAll();
        
        $stmt2 = $em->getConnection()->prepare($query2);
        $stmt2->execute();
        $res2 = $stmt2->fetchAll();
        
        return array($res, $res2);
    }
    
    private function printDTD()
    {
        $this->print .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    }
    
    private function printAll()
    {
        $this->print .= '<DeclarationReport xmlns="http://www.onegate.eu/2010-01-01">';
        $this->printAdministration();
        $this->printReport();
        $this->print .= '</DeclarationReport>';
    }
    
    private function printAdministration()
    {
        $this->print .= '<Administration creationTime="'.date('Y-m-d').'T'.date('H:i:s.000P').'">';
        $this->printAdministrationContent();
        $this->print .= '</Administration>';
    }
    
    private function printReport()
    {
        $this->print .= '<Report code="CRC" date="'.date('Y-m', mktime(12, 0, 0, date("m"), 0, date("Y"))).'">';
        if ($this->records[0] || $this->records[1]) {
            
            $this->print .= '<Data form="CRC">';
            foreach ($this->records[0] as $record) {
                $this->printItem($record, 'D');
            }
            foreach ($this->records[1] as $record) {
                $this->printItem($record, 'F');
            }
            $this->print .= '</Data>';
        } else {
            $this->print .= '<Data action="nihil" form="CRC"/>';
        }
        $this->print .= '</Report>';
    }
    
    private function printAdministrationContent()
    {
        $this->print .= '<From declarerType="SIREN_R">780076857</From>';
        $this->print .= '<To>BDF</To>';
        $this->print .= '<Domain>CRC</Domain>';
        $this->print .= '<Response>';
        $this->print .= '<Email>contact.banque@fiducial.fr</Email>';
        $this->print .= '<Language>FR</Language>';
        $this->print .= '</Response>';
    }
    
    private function printItem($record, $codeEco = 'F')
    {
        $this->print .= '<Item>';
        $this->print .= '<Dim prop="SIREN_D">'.trim($record["CLIENASRN"]).'</Dim>';
        $this->print .= '<Dim prop="PAYS_CTPT">'.trim($record["PAYS"]).'</Dim>';
        $this->print .= '<Dim prop="CODE_ECO">'.$codeEco.'</Dim>';
        $this->print .= '<Dim prop="SENS_TRSCT">2</Dim>';
        $this->print .= '<Dim prop="MTT_TRSCT">'.floor($record["MTT"]/1000).'</Dim>';
        $this->print .= '</Item>';
    }
    
    private function makeFile()
    {
        $directory = $this->getContainer()->getParameter('dirSortieCRC');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        
        $fileName = "crc_fiducial_".date('mY').".xml";
        $dateDir = $directory . '/' . date('Y-m') . '/';
        
        if (!file_exists($dateDir)) {
            mkdir($dateDir);
        }
        
        // Créer le fichier
        $fileManager->ecrireFichier($dateDir, $fileName, $this->print);
    }
}
