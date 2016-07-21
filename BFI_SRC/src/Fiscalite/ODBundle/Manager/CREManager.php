<?php

namespace Fiscalite\ODBundle\Manager;

/**
 * Description of CREManager
 *
 * @author d.briand
 */
class CREManager
{
    public $logManager;
    public $fileManager;
    public $entityManager;
    public $sabSFTP;

    private $print = "";
    private $counter = 0;
    private $directory = "";
    private $directorySab = "";
    private $isComplementaryDay = "0";
    private $baseFileName = "ZXCPTOD0";

    /**
     * Construction de la classe, on assigne les managers et les variables générales
     */
    public function __construct($lm, $fm, $em, $directory, $directorySab)
    {
        $this->logManager    = $lm;
        $this->fileManager   = $fm;
        $this->entityManager = $em->getManager('bfi');

        $this->directory     = $directory;
        $this->directorySab  = $directorySab;
    }

    public function setSabSFTP($sab)
    {
        $this->sabSFTP = $sab;
    }

    /**
     * Génération du fichier CRE
     */
    public function generate($isComplementaryDay = false)
    {
        if ($isComplementaryDay) {
            $this->isComplementaryDay = '1';
            $this->baseFileName       = "ZXCPTJC0";
        }

        $operations = $this->entityManager->getRepository('FiscaliteODBundle:Operation')->findBy(
            array('statut' => 'ENR', 'isDeleted' => 0, 'isComplementaryDay' => $this->isComplementaryDay)
        );

        // Tri à la mano du tableau d'objets car le numPiece est un "string"
        // et le schema update refuse ce changement de maniere simple
        usort($operations, function ($a, $b) {
            return (int)$a->getNumPiece() > (int)$b->getNumPiece();
        });

        if (count($operations) > 0) {
            foreach ($operations as $operation) {
                // Création du numéro de pièce définitif
                $operation->setNumPieceTech($this->getNewNumPiece());
                $this->entityManager->persist($operation);
                $this->entityManager->flush();
                
                $this->operationTreatment($operation);
            }

            $this->entityManager->flush();

            $this->moveFile();
            $this->addLog('Fichier généré avec '.$this->counter.' lignes.', 'success');
            return true;
        } else {
            $this->addLog('Fichier non généré, car aucune opération saisie.', 'info');
            return false;
        }
    }

    /*
     * Ecriture pour chaque opération
     */
    private function operationTreatment($operation)
    {
        $mouvements = $operation->getMouvements();
        $profil     = $operation->getProfil();

        if (!$profil) {
            $this->addLog(
                'L\'opération '.$operation->getNumPiece().' ne peut pas être importée. Aucun profil rattaché.'
            );
            return;
        }

        foreach ($mouvements as $mouvement) {
            $this->counter++;
            $this
                ->addToPrint($profil->getCodeEtabl(), 4)
                ->addToPrint("0101", 4)
                ->addToPrint("DC", 2, "A")
                ->addToPrint("DC", 2, "A")
                ->addToPrint("", 4, "A")
                ->addToPrint("001", 3)
                ->addToPrint(strtoupper($operation->getCodeOpe()), 3, "A")
                ->addToPrint($operation->getCodeEve(), 3, "A")
                ->addToPrint($operation->getNumPieceTech(), 9)
                ->addToPrint($mouvement->getNumMvmt(), 5)
                ->addToPrint("O", 1)
                ->addToPrint("002", 3)
                ->addToPrint($operation->getTiers(), 8, "N", 2)
                ->addToPrint($operation->getDateCpt()->format('1ymd'), 7)
                ->addToPrint($operation->getDateVal()->format('1ymd'), 7)
                ->addToPrint("", 42)
                ->addToPrint($operation->getDevise(), 3, "A", 2)
                ->addMontantToPrint($mouvement->getMontant(), "14;3")
                ->addCompteToPrint($mouvement->getCompte(), $mouvement->getMontant())
                ->addToPrint($operation->getRefLet(), 7, "A")
                ->addToPrint($mouvement->getLibelle(), 30, "A", 1, 'right')
                ->addToPrint($operation->getNumPieceTech(), 30, "A")
                ->addToPrint("1", 6, "A", 1, 'right')
                ->addToPrint("001", 6, "A", 1, 'right')
                ->addToPrint($operation->getRefAnalytique(), 6, "A", 1, 'right')
                ->addToPrint("0", 6, "A", 1, 'right')
                ->addToPrint("", 164, "A")
                ->addToPrint($mouvement->getCodeBudget(), 4)
                ->addToPrint("", 20, "A")
                ->addToPrint("", 61, "A")
            ;

            $this->print .= "\r\n";
        }

        $statutEnv = $this->entityManager->getRepository('FiscaliteODBundle:Statut')->findOneByIdStatut("ENV");
        $operation
            ->setStatutPrec($operation->getStatut())
            ->setDateStatutPrec($operation->getDateStatut())
            ->setStatut($statutEnv)
            ->setDateStatut(new \Datetime());

        $this->entityManager->persist($operation);
        $this->entityManager->flush();
    }

    /**
     * Ajout d'un montant à l'écriture
     * Formatage du contenu au format montant
     */
    private function addMontantToPrint($content, $size)
    {
        list($intPartSize, $decimalPartSize) = explode(";", $size);

        if (strstr($content, '.')) {
            $intPart = $this->addCaractere(str_replace("-", "", (int)$content), $intPartSize);
            $decimalPart = $this->addCaractere(
                substr($content, strpos($content, '.')+1),
                $decimalPartSize,
                "0",
                "right"
            );
        } else {
            $intPart     = $this->addCaractere(str_replace("-", "", (int)$content), $intPartSize);
            $decimalPart = $this->addCaractere("", $decimalPartSize);
        }

        $montantFinal = $intPart.$decimalPart;

        $zeroBefore = $content < 0 ? 0 : 17;
        $zeroAfter  = $content < 0 ? 119 : 102;

        $i = 0;
        while ($i < $zeroBefore) {
            $montantFinal = "0" . $montantFinal;
            $i++;
        }

        $j = 0;
        while ($j < $zeroAfter) {
            $montantFinal = $montantFinal . "0";
            $j++;
        }

        $this->addToPrint($montantFinal, $intPartSize+$decimalPartSize);

        return $this;
    }

    /**
     * Ajout d'un compte à l'écriture
     * Formatage du contenu au format voulu
     */
    private function addCompteToPrint($compte, $montant)
    {
        $compte = $this->addCaractere($compte, 20, ' ', 'right');

        $spacesBefore = $montant < 0 ? 2 : 24;
        $spacesAfter  = $montant < 0 ? 44 : 22;

        $i = 0;
        while ($i < $spacesBefore) {
            $compte = " " . $compte;
            $i++;
        }

        $j = 0;
        while ($j < $spacesAfter) {
            $compte = $compte . " ";
            $j++;
        }

        $this->addToPrint($compte, 66, "A");

        return $this;
    }

    /**
     * Ajout d'un contenu à l'écriture
     */
    private function addToPrint($content, $size, $type = "N", $occurencies = 1, $position = 'left')
    {
        if ($type == "A") {
            $character = " ";
        } elseif ($type == "N") {
            $character = "0";
        }

        for ($i = 0; $i < $occurencies; $i++) {
            $this->print .= $this->addCaractere($content, $size, $character, $position);
        }

        return $this;
    }

    /**
     * Ajout de caractères
     */
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

    /**
     * Ecriture et déplacement du fichier
     */
    private function moveFile()
    {
        $fileName = $this->baseFileName."_ODMULTI_".date('Ymd').".dat";
        $fNameSab = $this->baseFileName.".dat";

        // Créer le fichier
        $this->fileManager->ecrireFichier($this->directory, $fileName, $this->print);

        // Upload sur SAB
        if ($this->isComplementaryDay == '0') {
            $this->sabSFTP->upload($this->directory.$fileName, $this->directorySab.$fNameSab);
        }
    }

    /**
     * Ajout d'un log
     */
    private function addLog($message, $level = 'error')
    {
        switch ($level) {
            case 'error':
                $this->logManager->addError($message, 'OD > Module OD', 'Génération du fichier CRE');
                break;
            case 'alert':
                $this->logManager->addAlert($message, 'OD > Module OD', 'Génération du fichier CRE');
                break;
            case 'success':
                $this->logManager->addSuccess($message, 'OD > Module OD', 'Génération du fichier CRE');
                break;
            case 'info':
                $this->logManager->addInfo($message, 'OD > Module OD', 'Génération du fichier CRE');
                break;
        }
    }
    
    private function getNewNumPiece()
    {
        $statut = $this->entityManager->getRepository('FiscaliteODBundle:Statut')->findByIdStatut('ENV');
        $entity = $this->entityManager->getRepository('FiscaliteODBundle:Operation')->findBy(
            array('statut' => $statut),
            array('numPieceTech' => 'DESC'),
            1
        );
        
        echo $entity[0]->getNumPiece() . ' / ';
        
        $lastNum = $entity[0]->getNumPieceTech();
        
        return $lastNum + 1;
    }
}
