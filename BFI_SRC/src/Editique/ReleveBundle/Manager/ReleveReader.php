<?php

namespace Editique\ReleveBundle\Manager;

use Editique\ReleveBundle\Entity\Operation;

class ReleveReader
{
    private $content = '';
    private $currentLine = '';
    private $lines = [];
    private $oReleve = null;
    private $numFeuillet = 0;
    private $lectureManager = null;
    private $debutOpe = 0;
    private $finOpe = 0;

    public function setContent($content)
    {
        if (mb_detect_encoding($content) != 'UTF-8') {
            $this->content = iconv("Windows-1252", "UTF-8", $content);
        } else {
            $this->content = utf8_encode($content);
        }
        
        // On ajoute un \n avant la ligne total débiteur :
        // 08/09 : nous avons remarqué que les spools contenant 32 opérations étaient problématiques
        // 08/09 : Aucune ligne n'est passée entre la dernière opération et le total débiteur
        // 08/09 : notre script pense donc qu'il s'agit de la même ligne (split sur \n)
        // 08/09 : et un décalage à lieu. En ajoutant un \n, le prolbème ne sera plus.
        $pos = strpos($this->content, 'Total mouvements d');
        $this->content = substr_replace($this->content, "\n", $pos, 0);
        
        $this->lines = explode("\n", $this->content);
    }
    
    public function setLogManager($lm)
    {
        $this->logManager = $lm;
    }

    public function setLectureManager($lm)
    {
        $this->lectureManager = $lm;
    }

    public function getLectureManager()
    {
        return $this->lectureManager;
    }
    
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function getNumFeuillet()
    {
        $this->numFeuillet = (int)mb_substr($this->lines[20], 100, 5);
        return $this->numFeuillet;
    }

    public function lireReleve($oReleve)
    {
        if ($this->content != '' && $oReleve != null) {
            // init
            $this->debutOpe = 0;
            $this->finOpe = 0;
            
            $this->oReleve = $oReleve;

            foreach ($this->lines as $numLine => $line) {
                if ($numLine == 0) {
                    $this->oReleve->setModeDiffusion(mb_substr($line, 16, 1, 'utf-8'));
                }
                if ($numLine == 2) {
                    $this->oReleve->setDateDebut(mb_substr($line, 51, 10, 'utf-8'));
                    $this->oReleve->setDateFin(mb_substr($line, 65, 10, 'utf-8'));
                }
                if ($numLine == 14) {
                    $this->oReleve->setNumCompte(mb_substr($line, 11, 11, 'utf-8'));
                    $this->oReleve->setIntitule(mb_substr($line, 40, 30, 'utf-8'));
                }
                if ($numLine == 16) {
                    $this->oReleve->setIdEsab(mb_substr($line, 28, 9, 'utf-8'));
                }
                if ($numLine == 17) {
                    $this->oReleve->setIdClient(mb_substr($line, 25, 7, 'utf-8'));
                }
                if ($numLine == 19) {
                    $this->oReleve->setLibelleCompte(trim(mb_substr($line, 37, 30, 'utf-8')));
                }
                if ($numLine == 20) {
                    $this->oReleve->setNumReleve(trim(mb_substr($line, 80, 8, 'utf-8')));
                }
                if ($numLine == 23) {
                    $this->oReleve->setDateFinPrecedente(mb_substr($line, 30, 10, 'utf-8'));
                    $this->oReleve->setAncienSoldeDebiteur($this->lireMontant($line, 61, 20));
                    $this->oReleve->setAncienSoldeCrediteur($this->lireMontant($line, 86, 20));
                }
                if (strpos($line, 'Taux de rémunération brut') !== false) {
                    $this->oReleve->setTxRemuneration((double)trim(mb_substr($line, 28, 40, 'utf-8')));
                }
                if (strpos($line, 'Date de dernière mise à jour de la rémunération') !== false) {
                    $this->oReleve->setMajRemuneration(mb_substr($line, 50, 10, 'utf-8'));
                }
                if (strpos($line, 'Total des intérêts acquis') !== false) {
                    $this->oReleve->setTotalInteretAcquis($this->lireMontant($line, 29, 40));
                }
                if (strpos($line, 'Intérêts débiteur prélevés sur la période') !== false) {
                    $this->oReleve->setTotalInteretDebiteur($this->lireMontant($line, 45, 40));
                }
                if (strpos($line, 'Taux d\'intérêts brut appliqué sur la période') !== false) {
                    $this->oReleve->setTxInteret(trim(mb_substr($line, 47, 40, 'utf-8')));
                }
                if (strpos($line, 'TEG appliqué sur la période') !== false) {
                    $this->oReleve->setTEG(
                        (float)trim(str_replace(array('%', ','), array('', '.'), mb_substr($line, 31, 40, 'utf-8')))
                    );
                }
                if (strpos($line, 'Commission sur Solde débiteur') !== false) {
                    $this->oReleve->setCommissionsDebiteur($this->lireMontant($line, 33, 40));
                }
                if (strpos($line, 'Total mensuel des frais bancaires') !== false) {
                    $this->oReleve->setTotalCommissions($this->lireMontant($line, 37, 40));
                }
                if (strpos($line, 'NOUVEAU SOLDE AU') !== false) {
                    $this->oReleve->setDateFin(mb_substr($line, 30, 10, 'utf-8'));
                    $this->oReleve->setSoldeDebiteur($this->lireMontant($line, 61, 20));
                    $this->oReleve->setSoldeCrediteur($this->lireMontant($line, 86, 20));
                }
                if (strpos($line, 'Total mouvements débiteurs') !== false) {
                    $this->finOpe = $numLine - 1;
                    $this->oReleve->setTotalMvtDebiteur($this->lireMontant($line, 61, 20));
                }
                if (strpos($line, 'Total mouvements créditeurs') !== false) {
                    $this->oReleve->setTotalMvtCrediteur($this->lireMontant($line, 86, 20));
                }
                if ($this->numFeuillet == 1 &&
                    strpos($line, '(1 EUR = 6,55957 FRF)') !== false &&
                    isset($this->lines[$numLine + 1]) &&
                    strpos($this->lines[$numLine - 2], 'NOUVEAU SOLDE') === false
                ) {
                    $this->debutOpe = $numLine + 1;
                }
                if ($this->numFeuillet != 1 && strpos(utf8_decode($line), 'Report mouvements cr') !== false) {
                    $this->debutOpe = $numLine + 1;
                }
            }
            
            // on lit les operations
            $this->getOperations();
            $this->setTitulaire($this->oReleve->getIdClient());
        } else {
            //$this->addError('releve vide');
        }
    }
    
    private function setTitulaire($idClient)
    {
        $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL, ADRESSRA1, ADRESSRA2 FROM ZADRESS0 " .
            "WHERE ADRESSTYP = 1 AND ADRESSCOA = 'CO' AND ADRESSNUM = ".$idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res) {
            if (trim($res['ADRESSRA1']) || trim($res['ADRESSRA2'])) {
                $this->oReleve->setTitulaire1($res['ADRESSRA1']);
                $this->oReleve->setTitulaire2($res['ADRESSRA2']);
            } else {
                $this->setTitulaireFromCliena($idClient);
            }
        } else {
            $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL, ADRESSRA1, ADRESSRA2 FROM ZADRESS0 " .
                "WHERE ADRESSTYP = 1 AND ADRESSNUM = ".$idClient;

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
            
            if (trim($res['ADRESSRA1']) || trim($res['ADRESSRA2'])) {
                $this->oReleve->setTitulaire1($res['ADRESSRA1']);
                $this->oReleve->setTitulaire2($res['ADRESSRA2']);
            } else {
                $this->setTitulaireFromCliena($idClient);
            }
        }
        
        $adr = '';
        
        if (trim($res['ADRESSAD1'])) {
            $adr .= $res['ADRESSAD1'] . "\n";
        }
        if (trim($res['ADRESSAD2'])) {
            $adr .= $res['ADRESSAD2'] . "\n";
        }
        if (trim($res['ADRESSAD3'])) {
            $adr .= $res['ADRESSAD3'] . "\n";
        }
        
        $adr .= $res['ADRESSCOP'] . $res['ADRESSVIL'];
        $this->oReleve->setAdresse($adr);
        
        return $this;
    }
    
    private function setTitulaireFromCliena($idClient)
    {
        $q = "SELECT CLIENARA1, CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        $this->oReleve->setTitulaire1($res['CLIENARA1']);
        $this->oReleve->setTitulaire2($res['CLIENARA2']);
    }

    public function lireMontant($str, $debut, $taille)
    {
        $montant = mb_substr($str, $debut, $taille, 'utf-8');
        $montant = str_replace('.', '', $montant);
        $montant = str_replace(',', '.', $montant);
        return (float)$montant;
    }

    public function getIdClient($line)
    {
        $line = mb_substr($line, mb_strpos($line, '#', 0, 'utf-8'), 50, 'utf-8');
        return mb_substr($line, 24, 7, 'utf-8');
    }

    public function getOperations()
    {
        $currentOpe = null;

        // si on a pas reperer le debut des ope stop la
        if ($this->debutOpe == 0) {
            return;
        }

        for ($i = $this->debutOpe; $i <= $this->finOpe; $i++) {
            if (isset($this->lines[$i]) && trim($this->lines[$i]) != '') {
                $this->lectureManager->setCurrentLine($this->lines[$i], $i);
                $this->currentLine = $this->lines[$i];

                if ($this->isNouvelleOpe()) {
                    if ($currentOpe !== null) {
                        $this->oReleve->addOperation($currentOpe);
                    }

                    $currentOpe = new Operation();
                    $currentOpe->setDateOperation($this->getOpeDate());
                    $currentOpe->setDateValeur($this->getOpeDateValeur());
                    $currentOpe->setDebit($this->getOpeDebit());
                    $currentOpe->setCredit($this->getOpeCredit());
                }

                if ($currentOpe !== null) {
                    $currentOpe->setLibelle($currentOpe->getLibelle() . $this->getOpeLibelle() . "\n");
                }
            }
        }
        // pour ajouter la derniere
        if ($currentOpe != null) {
            $this->oReleve->addOperation($currentOpe);
        }
    }

    public function isNouvelleOpe()
    {
        return trim(substr($this->currentLine, 0, 10)) !== '';
    }

    public function getOpeDate()
    {
        return $this->lectureManager->getDate();
    }

    public function getOpeLibelle()
    {
        return mb_substr($this->currentLine, 11, 30, 'utf-8');
    }

    public function getOpeDateValeur()
    {
        return mb_substr($this->currentLine, 44, 10, 'utf-8');
    }

    public function getOpeDebit()
    {
        return $this->lireMontant($this->currentLine, 63, 18);
    }

    public function getOpeCredit()
    {
        return $this->lireMontant($this->currentLine, 88, 18);
    }
}
