<?php

namespace Monetique\CardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monetique\CardBundle\Entity\P30S;

class P30SCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monetique:generate:p30s')
            ->setDescription('Génére le fichier P30S à destination du CMCIC.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->print = "";
        $this->numEnr = 0;
        $this->nbLine = 0;
        $this->numFile = $this->getContainer()->get('doctrine')->getManager('bfi')
            ->getRepository('MonetiqueCardBundle:P30S')->getNumFile();
        
        // Pour les tests
        $accounts = $this->getAccounts();
        
        foreach ($accounts as $account) {
            $this->nbDet = 0;
            $this->numEnr++;
            $this->generateEntete();
            $this->generateDetail($account);
            $this->generateEnqueue();
            $this->generateAnomalie();
        }
        
        $this->logFile();
        $this->moveFile();
    }
    
    private function generateEntete()
    {
        $this->addToPrint(date('y'), 2);
        $this->addToPrint(date('z'), 3);
        $this->addToPrint('ENT', 6, 'A', 1, 'right');
        $this->addToPrint('13179', 5);
        $this->addToPrint('00001', 5);
        $this->addToPrint($this->numFile, 6);
        $this->addToPrint($this->numEnr, 8);
        $this->addToPrint(date('His'), 6);
        $this->addToPrint('E', 1);
        $this->addToPrint('P30S', 5, 'A', 1, 'right');
        $this->addToPrint('FRA', 3);
        $this->addToPrint('', 5, 'A');
        $this->addToPrint('', 145, 'A');
        $this->print .= "\r\n";
        
        $this->nbLine++;
    }
    
    private function generateDetail($account)
    {
        $this->nbDet++;
        
        $solde = $this->getSolde($account);
        
        $this->addToPrint(date('y'), 2);
        $this->addToPrint(date('z'), 3);
        $this->addToPrint('DET', 6, 'A', 1, 'right');
        $this->addToPrint('13179', 5);
        $this->addToPrint('00001', 5);
        $this->addToPrint($this->numFile, 6);
        $this->addToPrint($this->numEnr, 8);
        $this->addToPrint('STK', 3, 'A', 1, 'right'); // A voir
        $this->addToPrint($this->getRIB($account), 23);
        $this->addToPrint('', 19, 'A');
        $this->addToPrint('EUR', 3);
        $this->addToPrint('R', 1);
        $this->addToPrint('0', 1);
        $this->addToPrint($solde['signe'], 1);
        $this->addToPrint($solde['solde'], 10);
        $this->addToPrint('E', 1);
        $this->addToPrint('', 1, 'A');
        $this->addToPrint('CATEG', 5); // A voir
        $this->addToPrint('', 97, 'A');
        $this->print .= "\r\n";
        
        $this->nbLine++;
    }
    
    private function generateEnqueue()
    {
        $this->addToPrint(date('y'), 2);
        $this->addToPrint(date('z'), 3);
        $this->addToPrint('FIN', 6, 'A', 1, 'right');
        $this->addToPrint('13179', 5);
        $this->addToPrint('00001', 5);
        $this->addToPrint($this->numFile, 6);
        $this->addToPrint($this->numEnr, 8);
        $this->addToPrint($this->nbDet, 6);
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 129, 'A');
        $this->print .= "\r\n";
        
        $this->nbLine++;
    }
    
    private function generateAnomalie()
    {
        $this->addToPrint(date('y'), 2);
        $this->addToPrint(date('z'), 3);
        $this->addToPrint('ERR', 6, 'A', 1, 'right');
        $this->addToPrint('13179', 5);
        $this->addToPrint('00001', 5);
        $this->addToPrint($this->numFile, 6);
        $this->addToPrint($this->numEnr, 8);
        $this->addToPrint('NUM', 8); // Avoir
        $this->addToPrint('', 6, 'A');
        $this->addToPrint('', 151, 'A');
        $this->print .= "\r\n";
        
        $this->nbLine++;
    }
    
    private function addToPrint($content, $size, $type = "N", $occurencies = 1, $position = 'left')
    {
        if ($type == "A") {
            $character = " ";
        } if ($type == "N") {
            $character = "0";
        }

        for ($i = 0; $i < $occurencies; $i++) {
            $this->print .= $this->addCaractere($content, $size, $character, $position);
        }

        return $this;
    }
    
    private function addCaractere($content, $size, $caractere = '0', $position = 'left')
    {
        // on complete la chaine
        while (mb_strlen($content, 'utf-8') < $size) {
            if ($position == 'left') {
                $content = $caractere . $content;
            } else {
                $content = $content . $caractere;
            }
        }

        return $content;
    }
    
    private function logFile()
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $p30s = new P30S();
        
        $p30s->setDateEnr(new \DateTime);
        $p30s->setNbClient($this->numEnr);
        $p30s->setNbLine($this->nbLine);
        $p30s->setDirectory(date('ymd'));
        
        $em->persist($p30s);
        $em->flush();
    }
    
    private function moveFile()
    {
        $directory = $this->getContainer()->getParameter('dirSortieDivers');
        $directorySab = $this->getContainer()->getParameter('sabCore.dirSortie2');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $sabSFTP = $this->getContainer()->get('back_office_connexion.SabSFTP');
        
        $fileName = "XCT6P30S0.dat";
        $dateDir = $directory . '/' . date('ymd') . '/';
        
        if (!file_exists($dateDir)) {
            mkdir($dateDir);
        }
        
        // Créer le fichier
        $fileManager->ecrireFichier($dateDir, $fileName, $this->print);
        //$sabSFTP->upload($dateDir.$fileName, $directorySab.$fileName);
    }
    
    private function getAccounts()
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        
        // Numéro de dossier et prêt
        $query = "SELECT CARCONCOM FROM ZCARCON0 WHERE CARCONCET = '002' OR CARCONCET = '003'";

        $stmt = $em->getConnection()->prepare($query);
        $stmt->execute();
        $res = $stmt->fetchAll();

        foreach ($res as $cardNumber) {
            $accounts[] = $cardNumber['CARCONCOM'];
        }
        
        return $accounts;
    }
    
    private function getRIB($account)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        
        // Numéro de dossier et prêt
        $query = "SELECT concat(CPTRIBBNQ, concat(CPTRIBGUI, concat(SUBSTR(COMPTECOM, 1, 11), CPTRIBRIB))) AS RIB "
                . "FROM ZCOMPTE0, ZCPTRIB0 WHERE COMPTECOM = CPTRIBCPT AND COMPTECOM = '" . $account . "'";

        $stmt = $em->getConnection()->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch();
        
        return $res['RIB'];
    }
    
    private function getSolde($account)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        
        // Numéro de dossier et prêt
        $query = "SELECT SOLDECEN FROM ZSOLDE0 WHERE SOLDECOM = '" . $account . "'";

        $stmt = $em->getConnection()->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch();
        
        $solde = $res['SOLDECEN'] * 100;
        
        if (substr($solde, 0, 1) == '-') {
            $solde = substr($solde, 1);
            $signe = '';
        } else {
            $signe = '-';
        }
        
        return array('signe' => $signe, 'solde' => $solde);
    }
}
