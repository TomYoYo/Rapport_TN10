<?php

namespace Editique\CreditBundle\Manager;

use Editique\CreditBundle\Entity\Credit;
use Editique\CreditBundle\Entity\Echeance;

class EcheancierReader
{
    private $oCredit = null;
    private $lectureManager = null;

    public function setLogManager($lm)
    {
        $this->logManager = $lm;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function getLectureManager()
    {
        return $this->lectureManager;
    }

    public function lireXML($filePath)
    {
        @$xml = simplexml_load_file($filePath);
        if ($xml) {
            $this->creditArray = json_decode(json_encode(simplexml_load_file($filePath)), true);
            $this->oCredit = new Credit();
            $this->setContentFromXML();

            return $this->oCredit;
        } else {
            $this->logManager->addError(
                'Le fichier XML (' . $filePath . ') est vide ou comporte une/plusieurs erreur(s).',
                'Lecture du XML en entrée'
            );
        }
    }

    public function lireTXT($filePath)
    {
        // INIT
        $this->oCredit = new Credit();
        $finEcheances = false;
        $debEcheances = false;
        $capRestDu = null;
        $content = iconv("Windows-1252", "UTF-8", file_get_contents($filePath));
        $this->creditArray = explode("\n", $content);

        // Récupération dans le spool
        if (count($this->creditArray) < 10) {
            return false;
        }
        
        foreach ($this->creditArray as $numLine => $line) {
            if ($numLine == 1) {
                if (mb_substr($line, 120, 2) != 'Le') {
                    return false;
                }
                $dateEdition = trim(mb_substr($line, 123, 8, 'utf-8'));
                if (substr($dateEdition, 1, 1) == '/') {
                    $dateEdition = '0'.$dateEdition;
                }
                $this->oCredit->setDateEdition($dateEdition);
            } elseif ($numLine == 3) {
                if (!trim(mb_substr($line, 30, 7, 'utf-8')) || !trim(mb_substr($line, 38, 7, 'utf-8'))) {
                    return false;
                }
                
                $this->oCredit->setNumDos(trim(mb_substr($line, 30, 7, 'utf-8')));
                $this->oCredit->setNumPret(trim(mb_substr($line, 38, 7, 'utf-8')));
            } elseif ($numLine == 5) {
                $idClient = mb_substr($line, 30, 7, 'utf-8');
                if (trim($idClient) == '') {
                    return false;
                }
                $this->oCredit->setIdClient($idClient);
            } elseif ($numLine == 7) {
                $this->oCredit->setMontantPret($this->lireMontantTXT(mb_substr($line, 30, 20, 'utf-8')));
            } elseif ($numLine == 8) {
                $dureeBrute = mb_substr($line, 30, 17, 'utf-8');
                if (strpos($dureeBrute, 'Mois')) {
                    $duree = str_replace('Mois', '', $dureeBrute);
                } elseif (strpos($dureeBrute, 'Trimestre(s)')) {
                    $duree = str_replace('Trimestre(s)', '', $dureeBrute);
                    $duree = 3 * (int)$duree;
                } elseif (strpos($dureeBrute, 'Semestre(s)')) {
                    $duree = str_replace('Semestre(s)', '', $dureeBrute);
                    $duree = 6 * (int)$duree;
                } elseif (strpos($dureeBrute, 'Année(s)')) {
                    $duree = str_replace('An(s)', '', $dureeBrute);
                    $duree = 12 * (int)$duree;
                } else {
                    $duree = "Echéance unique";
                }
                $this->oCredit->setDuree($duree);
            } elseif ($numLine == 15) {
                $debEcheances = true;
            }
            if (strpos($line, 'Total') && !isset($this->creditArray[$numLine+1])) {
                $this->oCredit->setMontantCapital($this->lireMontantTXT(mb_substr($line, 38, 17, 'utf-8')));
                $this->oCredit->setTotalInteret($this->lireMontantTXT(mb_substr($line, 58, 14, 'utf-8')));
                $this->oCredit->setTotalHorsAssurance($this->lireMontantTXT(mb_substr($line, 75, 17, 'utf-8')));
                $this->oCredit->setTotalAssurance($this->lireMontantTXT(mb_substr($line, 95, 16, 'utf-8')));
                $this->oCredit->setTotalPaye($this->lireMontantTXT(mb_substr($line, 112, 17, 'utf-8')));
            }
            if ($numLine == count($this->creditArray)-2) {
                $finEcheances = true;
            }
            // Explication de la condition suivante :
            // On vérifie que la liste des échéances est commencée
            // On vérifie que la liste des échéances n'est pas terminée
            // On vérifie que la ligne commence par | => Montre que nous sommes sur le tableau des échéances
            // On vérifie que le 4e caractères n'est pas E => Montre que nous ne sommes pas sur le haut du tableau
            if ($debEcheances && !$finEcheances && substr($line, 0, 1) == '|' && substr($line, 4, 1) != 'E') {
                $oEcheance = new Echeance();
                $oEcheance->setNumEcheance(mb_substr($line, 2, 3, 'utf-8'));
                
                $datePlv = trim(mb_substr($line, 7, 8, 'utf-8'));
                if (substr($datePlv, 1, 1) == '/') {
                    $datePlv = '0'.$datePlv;
                }
                
                $oEcheance->setDatePlvt($datePlv);
                $oEcheance->setCapAmorti($this->lireMontantTXT(mb_substr($line, 38, 17, 'utf-8')));
                $oEcheance->setInteretEch($this->lireMontantTXT(mb_substr($line, 58, 14, 'utf-8')));
                $oEcheance->setMontantHorsAss($this->lireMontantTXT(mb_substr($line, 75, 17, 'utf-8')));
                $oEcheance->setMontantAss($this->lireMontantTXT(mb_substr($line, 95, 14, 'utf-8')));
                $oEcheance->setTotalEcheance($this->lireMontantTXT(mb_substr($line, 112, 17, 'utf-8')));

                if ($capRestDu) {
                    $capRestDu = $capRestDu - $capAmorti;
                } else {
                    $capRestDu = $this->oCredit->getMontantPret(false);
                }
                
                $oEcheance->setCapRestDu($capRestDu);
                $capAmorti = $oEcheance->getCapAmorti();

                $this->oCredit->addEcheance($oEcheance);
            }
        }

        // Titre
        $this->oCredit->setTitre(
            $this->getTitreFromDatabase($this->oCredit->getNumDos(), $this->oCredit->getNumPret())
        );
        
        $this->setTitulaire($this->oCredit->getIdClient());

        // Valeurs en base
        $this->setRaisonsSocialeTXT($this->oCredit->getIdClient());
        $this->setTauxTXT($this->oCredit->getNumDos(), $this->oCredit->getNumPret());

        return $this->oCredit;
    }

    public function lireBDD($id)
    {
        // Numéro de dossier et prêt
        $q0 = "SELECT CREPLADOS, CREPLAPRE FROM ZCREPLA0 WHERE ID = ".$id;

        $stmt0 = $this->entityManager->getConnection()->prepare($q0);
        $stmt0->execute();
        $res0 = $stmt0->fetch();

        $numDos = $res0['CREPLADOS'];
        $numPre = $res0['CREPLAPRE'];
        
        if (!trim($numDos) || !trim($numPre)) {
            return false;
        }

        // TRAITEMENT, TITRE & MONTANT PRET
        $q = "SELECT CREPRECTA, CREPREMON FROM ZCREPRE0 WHERE CREPREDOS = ".$numDos." AND CREPREPRE = ".$numPre;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res['CREPRECTA'] == 4) {
            $this->oCredit = new Credit();
            $this->oCredit->setTitre("TABLEAU D'AMORTISSEMENT PREVISIONNEL");
            $this->oCredit->setMontantPret($res['CREPREMON']);
        } elseif ($res['CREPRECTA'] == 5 || $res['CREPRECTA'] == 6 || $res['CREPRECTA'] == 7) {
            $this->oCredit = new Credit();
            $this->oCredit->setTitre("TABLEAU D'AMORTISSEMENT");
            $this->oCredit->setMontantPret($res['CREPREMON']);
        } else {
            return false;
        }

        $this->oCredit->setNumDos($numDos);
        $this->oCredit->setNumpret($numPre);

        // EMPRUNTEUR
        $q2 = "SELECT CREEMPNCL FROM ZCLIENA0, ZCREEMP0 WHERE CLIENACLI = CREEMPNCL" .
                " AND CREEMPDOS = ".$numDos;

        $stmt2 = $this->entityManager->getConnection()->prepare($q2);
        $stmt2->execute();
        $res2 = $stmt2->fetch();

        $this->oCredit->setIdClient($res2['CREEMPNCL'] ?: 0);

        $this->setTitulaire($this->oCredit->getIdClient());

        // INFORMATIONS CREDIT
        $q4 = "SELECT CREPLATAF, CREPLAMAR, CREPLARTA, CREPLARTA FROM ZCREPLA0 WHERE CREPLADOS = ".$numDos.
                " AND CREPLAPRE = ".$numPre;

        $stmt4 = $this->entityManager->getConnection()->prepare($q4);
        $stmt4->execute();
        $res4 = $stmt4->fetch();

        $this->oCredit->setTypeTaux($res4['CREPLARTA']);
        $this->oCredit->setTaux($res4['CREPLATAF']);
        $this->oCredit->setMargeTaux($res4['CREPLAMAR']);
        $this->oCredit->setCodeTaux($res4['CREPLARTA']);

        if (trim($res4['CREPLARTA'])) {
            $q4bis = "SELECT BASTAULIB FROM ZBASTAU0 WHERE BASTAUTAU = '".$res4['CREPLARTA']."'";

            $stmt4bis = $this->entityManager->getConnection()->prepare($q4bis);
            $stmt4bis->execute();
            $res4bis = $stmt4bis->fetch();

            $this->oCredit->setCodeTaux($res4bis['BASTAULIB']);
        }

        // TEG
        $q5 = "SELECT CREBI2TEG FROM ZCREBI20 WHERE CREBI2DOS = ".$numDos." AND CREBI2PRE = ".$numPre;

        $stmt5 = $this->entityManager->getConnection()->prepare($q5);
        $stmt5->execute();
        $res5 = $stmt5->fetch();

        $this->oCredit->setTAEG($res5['CREBI2TEG']);

        // DUREE
        $q6 = "SELECT CREPLANPC, CREPLAPCA FROM ZCREPLA0 WHERE CREPLADOS = ".$numDos;

        $stmt6 = $this->entityManager->getConnection()->prepare($q6);
        $stmt6->execute();
        $res6 = $stmt6->fetch();

        $duree = 0;

        if (isset($res6[0])) {
            foreach ($res6 as $line) {
                if ($line['CREPLAPCA'] == 'M') {
                    $duree += $line['CREPLANPC'];
                } elseif ($line['CREPLAPCA'] == 'A') {
                    $duree += $line['CREPLANPC']*12;
                } elseif ($line['CREPLAPCA'] == 'T') {
                    $duree += $line['CREPLANPC']*3;
                } elseif ($line['CREPLAPCA'] == 'S') {
                    $duree += $line['CREPLANPC'*6];
                } elseif ($line['CREPLAPCA'] == 'U') {

                }
            }
            $this->oCredit->setDuree($duree);
        } else {
            if ($res6['CREPLAPCA'] == 'M') {
                $duree = $res6['CREPLANPC'];
            } elseif ($res6['CREPLAPCA'] == 'A') {
                $duree = $res6['CREPLANPC']*12;
            } elseif ($res6['CREPLAPCA'] == 'T') {
                $duree = $res6['CREPLANPC']*3;
            } elseif ($res6['CREPLAPCA'] == 'S') {
                $duree = $res6['CREPLANPC'*6];
            } elseif ($res6['CREPLAPCA'] == 'U') {

            }
            $this->oCredit->setDuree($duree);
        }

        // ECHEANCES
        $q7 = "SELECT * FROM ZCREBIS0 WHERE CREBISDOS = " . $numDos . " AND CREBISPRE = " . $numPre .
                " AND CREBISTYP IN ('02', '03', '04') ORDER BY CREBISECH";

        $stmt7 = $this->entityManager->getConnection()->prepare($q7);
        $stmt7->execute();
        $res7 = $stmt7->fetchAll();

        $capRestDu = 0;
        $montantCapital = $totalInteret = $totalHorsAss = $totalAss = 0;
        foreach ($res7 as $ech) {
            $oEcheance = new Echeance();
            $oEcheance->setNumEcheance($ech['CREBISECH']);
            $oEcheance->setCapAmorti($ech['CREBISMAM']);
            $montantCapital += $ech['CREBISMAM'];
            $oEcheance->setInteretEch($ech['CREBISMIN']);
            $totalInteret += $ech['CREBISMIN'];
            $oEcheance->setMontantHorsAss($ech['CREBISMAM'] + $ech['CREBISMIN']);
            $totalHorsAss += $ech['CREBISMAM'] + $ech['CREBISMIN'];
            $oEcheance->setMontantAss($ech['CREBISASC']);
            $totalAss += $ech['CREBISASC'];
            $oEcheance->setTotalEcheance($ech['CREBISMRE']);
            $oEcheance->setDatePlvt($this->lireDateBDD($ech['CREBISDTR']));

            if ($capRestDu) {
                $capRestDu = $capRestDu - $capAmorti;
            } else {
                $capRestDu = $this->oCredit->getMontantPret(false);
            }
            $oEcheance->setCapRestDu($capRestDu);
            $capAmorti = $oEcheance->getCapAmorti();

            $this->oCredit->addEcheance($oEcheance);
        }

        // TOTAUX
        $this->oCredit->setMontantCapital($montantCapital);
        $this->oCredit->setTotalInteret($totalInteret);
        $this->oCredit->setTotalHorsAssurance($totalHorsAss);
        $this->oCredit->setTotalAssurance($totalAss);
        $this->oCredit->setTotalPaye($totalHorsAss + $totalAss);

        // VALEUR EN DUR
        $datetime = new \Datetime();
        $this->oCredit->setDateEdition($datetime->format('d/m/Y'));

        return $this->oCredit;
    }
    
    private function getTitreFromDatabase($numDos, $numPre)
    {
        // TRAITEMENT, TITRE & MONTANT PRET
        $q = "SELECT CREPRECTA FROM ZCREPRE0 WHERE CREPREDOS = " . $numDos . " AND CREPREPRE = " . $numPre;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res['CREPRECTA'] == 5 || $res['CREPRECTA'] == 6 || $res['CREPRECTA'] == 7) {
            return "TABLEAU D'AMORTISSEMENT";
        }
        
        return "TABLEAU D'AMORTISSEMENT PREVISIONNEL";
    }

    private function setContentFromXML()
    {
        // VALUERS DANS LE XML
        $bodyCredit = $this->creditArray['body'];
        $this->oCredit->setNumPret($bodyCredit['numPre'] ?: null);
        $this->oCredit->setNumDos($bodyCredit['numDos'] ?: null);
        $this->oCredit->setMontantPret($bodyCredit['coAsDto']['totTtc'] ?: null);
        $this->oCredit->setMontantCapital($bodyCredit['tADto']['totAmo'] ?: null);
        $this->oCredit->setTotalInteret($bodyCredit['tADto']['totInt'] ?: null);
        $this->oCredit->setTotalHorsAssurance($bodyCredit['tADto']['totEch'] ?: null);
        $this->oCredit->setTotalAssurance($bodyCredit['tADto']['totAss'] ?: null);
        $this->oCredit->setTotalPaye($bodyCredit['tADto']['tToEch'] ?: null);

        // VALEUR EN DUR
        $datetime = new \Datetime();
        $this->oCredit->setDateEdition($datetime->format('d/m/Y'));
        $this->oCredit->setTitre("TABLEAU D'AMORTISSEMENT PREVISIONNEL");

        // VALUES EN TABLE
        $this->setEmprunteurXML();
        $this->setTitulaire($this->oCredit->getIdClient());
        $this->setDureeXML();
        $this->setTauxXML();
        $this->setTEGXML();

        // ECHEANCES
        foreach ($bodyCredit['lstEchea']['echea'] as $echeance) {
            $oEcheance = new Echeance();
            $oEcheance->setNumEcheance($echeance['numEch'] ?: null);
            $oEcheance->setDatePlvt($this->lireDateXML($echeance['datEch']) ?: null);
            $oEcheance->setCapRestDu($echeance['crdAv'] ?: null);
            $oEcheance->setCapAmorti($echeance['mntAmo'] ?: null);
            $oEcheance->setInteretEch($echeance['mntInt'] ?: null);
            $oEcheance->setMontantHorsAss($echeance['mntEch'] ?: null);
            $oEcheance->setMontantAss($echeance['mntAss'] ?: null);
            $oEcheance->setTotalEcheance($echeance['mntTot'] ?: null);

            $this->oCredit->addEcheance($oEcheance);
        }
    }

    private function setEmprunteurXML()
    {
        $bodyCredit = $this->creditArray['body'];

        $q = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%01%' AND ETUCRECDO = 'NUMCLI'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt = $this->entityManager->getConnection()->prepare($q);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            if (!empty($res)) {
                $parameters = explode(';', substr($res['ETUCREDON'], 1));
                $this->oCredit->setIdClient($parameters[0]);
                $this->oCredit->setRaisonSocial1($parameters[1]);
                $this->oCredit->setRaisonSocial2($parameters[2]);
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    private function setAdresseXML()
    {
        $bodyCredit = $this->creditArray['body'];

        $q = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%01%' AND ETUCRECDO = 'ADRE1'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt = $this->entityManager->getConnection()->prepare($q);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            if (!empty($res)) {
                $parameters = explode(';', substr($res['ETUCREDON'], 1));
                $this->oCredit->setAdresse1($parameters[0]);
                $this->oCredit->setAdresse2($parameters[1]);
                $this->oCredit->setAdresse3($parameters[2]);
                $this->oCredit->setCodePostal($parameters[3]);
                $this->oCredit->setVille($parameters[4]);
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    private function setDureeXML()
    {
        $bodyCredit = $this->creditArray['body'];

        $q = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%05%' AND ETUCRECDO = 'DUREE'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt = $this->entityManager->getConnection()->prepare($q);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            if (!empty($res)) {
                $this->oCredit->setDuree(trim((int)substr($res['ETUCREDON'], 1)));
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    private function setTauxXML()
    {
        $bodyCredit = $this->creditArray['body'];

        $q = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%05%' AND ETUCRECDO = 'TYPTAU'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt = $this->entityManager->getConnection()->prepare($q);

        $q2 = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%05%' AND ETUCRECDO = 'TAUFIX'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt2 = $this->entityManager->getConnection()->prepare($q2);

        $q3 = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%05%' AND ETUCRECDO = 'MARGE'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt3 = $this->entityManager->getConnection()->prepare($q3);

        $q4 = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%05%' AND ETUCRECDO = 'CODTAU'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt4 = $this->entityManager->getConnection()->prepare($q4);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            $stmt2->execute();
            $res2 = $stmt2->fetch();

            $stmt3->execute();
            $res3 = $stmt3->fetch();

            $stmt4->execute();
            $res4 = $stmt4->fetch();

            if (!empty($res) && !empty($res2)) {
                $typeTaux = substr($res['ETUCREDON'], 1, 17);
                $tauxPret = $this->extractInfoPretFromSab($res2['ETUCREDON']);
                $margePret = $this->extractInfoPretFromSab($res3['ETUCREDON']);
                $codeTaux = substr(trim($res4['ETUCREDON']), 21, 70);

                $this->oCredit->setTypeTaux($typeTaux);
                $this->oCredit->setTaux($tauxPret);
                $this->oCredit->setMargeTaux($margePret);
                $this->oCredit->setCodeTaux($codeTaux);
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    // jd je fais une function car je suis tomve sur ca (jusque colonne 408 sorry phpcs)
    // ;   6,600 % (+ pleins d'espace et un 0)
    private function extractInfoPretFromSab($res)
    {
        $tmp = explode('%', $res);
        $res = (float)str_replace(';', '', $tmp[0]);
        return number_format($res, 2, '.', ' ');
    }

    private function setTEGXML()
    {
        $bodyCredit = $this->creditArray['body'];

        $q = "SELECT ETUCREDON FROM ZETUCRE0 WHERE ETUCRETDO LIKE '%09%' AND ETUCRECDO = 'TEG'" .
            " AND ETUCREDOS = " . $bodyCredit['numDos'] . " AND ETUCRENPR = " . $bodyCredit['numPre'] .
            " AND ETUCRECOU = 'OFFRE'";

        $stmt = $this->entityManager->getConnection()->prepare($q);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            if (!empty($res)) {
                $teg = $this->extractInfoPretFromSab($res['ETUCREDON']);
                $this->oCredit->setTaeg($teg);
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    private function setRaisonsSocialeTXT($idClient)
    {
        $q = "SELECT CLIENARA1,CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);

        try {
            $stmt->execute();
            $res = $stmt->fetch();

            if (!empty($res)) {
                $this->oCredit->setRaisonSocial1($res['CLIENARA1']);
                $this->oCredit->setRaisonSocial2($res['CLIENARA2']);
            }
        } catch (\Exception $ex) {
            $this->logManager->addError($ex);
        }
    }

    private function setTauxTXT($numDos, $numPre)
    {
        $q = "SELECT CREPLATAF, CREPLAMAR, CREPLARTA, CREPLARTA FROM ZCREPLA0 WHERE CREPLADOS = " . $numDos .
            " AND CREPLAPRE = " . $numPre;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        $this->oCredit->setTypeTaux($res['CREPLARTA']);
        $this->oCredit->setTaux($res['CREPLATAF']);
        $this->oCredit->setMargeTaux($res['CREPLAMAR']);
        
        if (trim($res['CREPLARTA'])) {
            $q2 = "SELECT BASTAULIB FROM ZBASTAU0 WHERE BASTAUTAU = '" . $res['CREPLARTA'] . "'";

            $stmt2 = $this->entityManager->getConnection()->prepare($q2);
            $stmt2->execute();
            $res2 = $stmt2->fetch();

            $this->oCredit->setCodeTaux($res2['BASTAULIB']);
        }
        
        // TEG
        $q2 = "SELECT CREBI2TEG FROM ZCREBI20 WHERE CREBI2DOS = ".$numDos." AND CREBI2PRE = ".$numPre;

        $stmt2 = $this->entityManager->getConnection()->prepare($q2);
        $stmt2->execute();
        $res2 = $stmt2->fetch();

        $this->oCredit->setTAEG($res2['CREBI2TEG']);
    }

    private function lireDateXML($chaine)
    {
        $year = substr($chaine, 0, 4);
        $month = substr($chaine, 4, 2);
        $day = substr($chaine, 6, 2);

        return $day.'/'.$month.'/'.$year;
    }

    private function lireMontantTXT($chaine)
    {
        if (trim($chaine) && is_numeric(str_replace(array('.',','), array('','.'), trim($chaine)))) {
            return number_format(str_replace(array('.',','), array('','.'), trim($chaine)), 2, '.', '');
        }

        return 0;
    }

    private function lireDateBDD($chaine)
    {
        $year = substr($chaine, 1, 2);
        $month = substr($chaine, 3, 2);
        $day = substr($chaine, 5, 2);

        return $day.'/'.$month.'/20'.$year;
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
                $this->oCredit->setRaisonSocial1($res['ADRESSRA1']);
                $this->oCredit->setRaisonSocial2($res['ADRESSRA2']);
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
                $this->oCredit->setRaisonSocial1($res['ADRESSRA1']);
                $this->oCredit->setRaisonSocial2($res['ADRESSRA2']);
            } else {
                $this->setTitulaireFromCliena($idClient);
            }
        }
        
        $this->oCredit->setAdresse1($res['ADRESSAD1']);
        $this->oCredit->setAdresse2($res['ADRESSAD2']);
        $this->oCredit->setAdresse3($res['ADRESSAD3']);
        $this->oCredit->setCodePostal($res['ADRESSCOP']);
        $this->oCredit->setVille($res['ADRESSVIL']);
        
        return $this;
    }
    
    private function setTitulaireFromCliena($idClient)
    {
        $q = "SELECT CLIENARA1, CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        $this->oCredit->setRaisonSocial1($res['CLIENARA1']);
        $this->oCredit->setRaisonSocial2($res['CLIENARA2']);
    }
    
    private function addLog($libelle, $action, $type = 'error')
    {
        switch ($type) {
            case 'error':
            case 'fatal':
                $this->logManager->addError($libelle, 'Editique > Compte', $action);
                break;
            case 'alert':
                $this->logManager->addAlert($libelle, 'Editique > Compte', $action);
                break;
            case 'info':
                $this->logManager->addInfo($libelle, 'Editique > Compte', $action);
                break;
            case 'success':
                $this->logManager->addSuccess($libelle, 'Editique > Compte', $action);
                break;
        }
        
        if ($type == 'fatal') {
            $this->addFatalError($libelle);
        }
    }
}
