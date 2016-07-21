<?php

namespace BackOffice\MonitoringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrosselingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitoring:crosseling:expertise')
            ->setDescription('Récupère les clients apportés par expertise.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialisation des variables
        $this->em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $this->flux = "";
        $this->fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $directory = $this->getContainer()->getParameter('dirSortieCrosseling');
        $this->dirSortie = $directory . '/' . date('Y-m') . '/';
        
        // DWM Manager
        $this->dwmManager = $this->getContainer()->get('back_office_connexion.dwmSFTP');
        $directory = $this->getContainer()->getParameter('dwm.dirDepot');
        
        // Lancement requête
        $clients = $this->getClients();
        
        // Base du flux (3 lignes blanches)
        $this->addToPrint("\r\n", 0, false);
        $this->addToPrint("\r\n", 0, false);
        $this->addToPrint("\r\n", 0, false);
        
        // Inscription du flux
        foreach ($clients as $client) {
            $this->addToPrint($client['NUM_CLI_BNQ'], 9);
            $this->addToPrint($client['LIB_CLI_BNQ'], 90);
            $this->addToPrint($client['NUM_SRN_BNQ'], 9);
            $this->addToPrint($client['COD_ANL_BNQ'], 5);
            $this->addToPrint($client['DTE_OUV_CPT'], 10);
            $this->addToPrint(date('d/m/Y'), 10, false);
            $this->addToPrint("\r\n", 0, false);
        }
        
        // Ecriture du fichier
        $this->makeFile();
        
        // Copie et transfert
        $this->dwmManager->upload($this->dirSortie.$this->fileName, $directory.$this->fileName);
    }
    
    private function getClients()
    {
        // Requête fournie par Julien C. dans les spécifications
        $req = "
            SELECT A.DWHCPTPPAL AS NUM_CLI_BNQ,
            TRIM(CONCAT(TRIM(B.CLIENARA1), CONCAT(' ',TRIM(B.CLIENARA2)))) AS LIB_CLI_BNQ,
            R.DWHCLISRN AS NUM_SRN_BNQ,
            SUBSTR(RTRIM(M.APOAPPRES), 1, 5) AS COD_ANL_BNQ,
            TO_CHAR(TO_DATE(SUBSTR(B.CLIENACRE,2), 'YYMMDD'), 'DD/MM/YYYY') AS DTE_OUV_CPT
            
            FROM ZDWHCPT0 A
            LEFT JOIN SAB139.ZCLIENA0 B ON (A.DWHCPTPPAL = B.CLIENACLI)
            LEFT JOIN SAB139.ZDWHABO0 I ON (I.DWHABOCLI = A.DWHCPTPPAL AND I.DWHABODTX = A.DWHCPTDTX)
            LEFT JOIN SAB139.ZADRESS0 K ON (TRIM(K.ADRESSNUM) = A.DWHCPTPPAL)
            LEFT JOIN SAB139.ZAPOOPE0 L ON (A.DWHCPTPPAL = TRIM(APOOPEREF)
              OR A.DWHCPTPPAL = CONCAT(0, SUBSTR(APOOPEREF, 4, 6)))
            LEFT JOIN SAB139.ZAPOAPP0 M ON (L.APOOPEAPP = M.APOAPPAPP)
            LEFT JOIN SAB139.ZCLIPIA0 P ON (A.DWHCPTPPAL = P.CLIPIACLI)
            LEFT JOIN SAB139.ZDWHCLI0 R ON (A.DWHCPTPPAL = R.DWHCLICLI)
            
            WHERE I.DWHABOPRO IN ('003', '004', '005', '006', '007')
            AND K.ADRESSCOA NOT IN ('CO', 'CH')
            AND CONCAT(0, SUBSTR(APOOPEREF, 4, 6)) NOT IN (SELECT TRIM(APOOPEREF) FROM SAB139.ZAPOOPE0)
            AND DWHCLIDTX IN (SELECT MAX(DWHCLIDTX) FROM SAB139.ZDWHCLI0)
            AND DWHABODTX IN (SELECT MAX(DWHABODTX) FROM SAB139.ZDWHABO0)
            GROUP BY A.DWHCPTPPAL, B.CLIENARA1, B.CLIENARA2, R.DWHCLISRN, M.APOAPPRES, B.CLIENACRE, TRUNC(sysdate)
            ORDER BY A.DWHCPTPPAL
        ";
        
        $stmt = $this->em->getConnection()->prepare($req);
        $stmt->execute();
        $res = $stmt->fetchAll();
        
        return $res;
    }
    
    private function makeFile()
    {
        $this->fileName = "crs_banque.txt";
        
        // Créer le dossier si besoin
        if (!file_exists($this->dirSortie)) {
            mkdir($this->dirSortie);
        }

        // Créer le fichier
        $this->fileManager->ecrireFichier($this->dirSortie, $this->fileName, $this->flux);
    }
    
    private function addToPrint($content, $size, $addSep = true)
    {
        $this->flux .= $this->addSpace($content, $size);
        
        if ($addSep) {
            $this->flux .= ";";
        }
    }
    
    private function addSpace($content, $size)
    {
        // on complete la chaine
        while (mb_strlen($content, 'utf-8') < $size) {
            $content = $content . " ";
        }

        return $content;
    }
}
