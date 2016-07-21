<?php

namespace Editique\CreditBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Manager pour la souscription de crédit
 *
 * @author d.briand
 */
class CreditManager
{
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }
    
    public function getEmprunteurWithCredit($numDos, $numPret)
    {
        if ($numDos && $numPret) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CREEMPNCL FROM ZCREEMP0 WHERE CREEMPDOS = ".$numDos." AND CREEMPSEQ = ".$numPret);

            $stmt->execute();
            $res = $stmt->fetch();
            
            return $res['CREEMPNCL'];
        }

        return false;
    }
    
    public function getPlanWithCredit($numDos, $numPret)
    {
        if ($numDos && $numPret) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CREPLATAF, CREPLAMAR, CREPLARTA FROM ZCREPLA0 WHERE CREPLADOS = " .
                    $numDos . " AND CREPLAPRE = " . $numPret
                );

            $stmt->execute();
            $res = $stmt->fetch();
            
            $arr['txNomAnn'] = $res['CREPLATAF'];
            $arr['margeTx'] = $res['CREPLAMAR'];
            $arr['codeTx'] = $res['CREPLARTA'];
            
            // Nb echéance, période, durée
            $stmtBis = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CREPLANPC, CREPLAPCA FROM ZCREPLA0 WHERE CREPLADOS = " .
                    $numDos . " AND CREPLAPRE = " . $numPret . " ORDER BY CREPLADEC"
                );

            $stmtBis->execute();
            $resBis = $stmtBis->fetchAll();
            
            if (count($resBis) > 1) {
                $arr['duree'] = 0;
                $arr['nbEch'] = 0;
                
                foreach ($resBis as $line) {
                    $arr['nbEch'] += $line['CREPLANPC'];
                    
                    switch ($line['CREPLAPCA']) {
                        case 'A':
                            $arr['perInt'] = 'Annuelle';
                            $arr['duree'] += $line['CREPLANPC'] * 12;
                            break;
                        case 'S':
                            $arr['perInt'] = 'Semestrielle';
                            $arr['duree'] += $line['CREPLANPC'] * 6;
                            break;
                        case 'T':
                            $arr['perInt'] = 'Trimestrielle';
                            $arr['duree'] += $line['CREPLANPC'] * 3;
                            break;
                        case 'M':
                            $arr['perInt'] = 'Mensuelle';
                            $arr['duree'] += $line['CREPLANPC'];
                            break;
                        case 'U':
                            $arr['perInt'] = 'Unique';
                            $arr['duree'] = "Echéance unique";
                            break;
                        default:
                            $arr['perInt'] = null;
                            $arr['duree'] = null;
                            break;
                    }
                }
            } else {
                $arr['nbEch'] = $resBis[0]['CREPLANPC'];
            
                switch ($resBis[0]['CREPLAPCA']) {
                    case 'A':
                        $arr['perInt'] = 'Annuelle';
                        $arr['duree'] = $resBis[0]['CREPLANPC'] * 12;
                        break;
                    case 'S':
                        $arr['perInt'] = 'Semestrielle';
                        $arr['duree'] = $resBis[0]['CREPLANPC'] * 6;
                        break;
                    case 'T':
                        $arr['perInt'] = 'Trimestrielle';
                        $arr['duree'] = $resBis[0]['CREPLANPC'] * 3;
                        break;
                    case 'M':
                        $arr['perInt'] = 'Mensuelle';
                        $arr['duree'] = $resBis[0]['CREPLANPC'];
                        break;
                    case 'U':
                        $arr['perInt'] = 'Unique';
                        $arr['duree'] = "Echéance unique";
                        break;
                    default:
                        $arr['perInt'] = null;
                        $arr['duree'] = null;
                        break;
                }
            }
            
            // Cod libellé
            if (trim($res['CREPLARTA']) != '') {
                $stmt2 = $this->entityManager
                    ->getConnection()
                    ->prepare("SELECT BASTAULIB FROM ZBASTAU0, ZCREPLA0 WHERE BASTAUTAU = '" . $res['CREPLARTA'] . "'");

                $stmt2->execute();
                $res2 = $stmt2->fetch();

                $arr['libCodeTx'] = $res2['BASTAULIB'];
            } else {
                $arr['libCodeTx'] = null;
            }
            
            // Mode amortissement
            $stmt3 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CRE179007 FROM ZCRE1790, ZCREPLA0 WHERE CREPLADOS = " .
                    $numDos . " AND CREPLAMOA = CRE179003"
                );

            $stmt3->execute();
            $res3 = $stmt3->fetch();

            $arr['modeAmo'] = ucfirst(strtolower($res3['CRE179007']));
            
            return $arr;
        }

        return false;
    }
    
    public function getCreBisWithCredit($numDos, $numPret)
    {
        if ($numDos && $numPret) {
            // CPT SUPPORT
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT DISTINCT(CREBISCOM) FROM ZCREBIS0 WHERE CREBISDOS = " . $numDos . " AND CREBISPRE = " .
                    $numPret . " AND CREBISTYP IN ('02', '03', '04')"
                );

            $stmt->execute();
            $res = $stmt->fetch();
            
            $arr['cptSupport'] = $res['CREBISCOM'];
            
            // FRAIS DOS
            $stmt2 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CREBISMAM FROM ZCREBIS0 WHERE CREBISDOS = " . $numDos . " AND CREBISPRE = " . $numPret .
                    " AND CREBISCAS = 'FRDOS'"
                );

            $stmt2->execute();
            $res2 = $stmt2->fetch();
            
            $arr['fraisDos'] = $res2['CREBISMAM'];
            
            // FRAIS GAR
            $stmt3 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT TRIM(CREBISMAM) as montant, TRIM(BAS044009) as libelle FROM ZCREBIS0, ZBAS0440" .
                    " WHERE CREBISDOS = " . $numDos .
                    " AND (CREBISCAS != 'FRDOS' AND TRIM(CREBISCAS) IS NOT NULL) AND CREBISCAS = BAS044003"
                );

            $stmt3->execute();
            $res3 = $stmt3->fetchAll();
            
            $arr['fraisGar'] = $res3;
            
            // Date dernière échéance
            $stmt4 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT MAX(DISTINCT(CREBISFIN)) AS CREBISFINMAX FROM ZCREBIS0 WHERE CREBISDOS = " . $numDos .
                    " AND CREBISPRE = " . $numPret
                );

            $stmt4->execute();
            $res4 = $stmt4->fetch();
            
            $arr['dtDerEch'] =
                substr($res4['CREBISFINMAX'], 5, 2) . '/' .
                substr($res4['CREBISFINMAX'], 3, 2) . '/20' .
                substr($res4['CREBISFINMAX'], 1, 2);
            
            // TEG (Attention, table ZCREBI20 et non pas ZCREBIS0)
            $stmt5 = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CREBI2TE1 FROM ZCREBI20 WHERE CREBI2DOS = ".$numDos." AND CREBI2PRE = ".$numPret);

            $stmt5->execute();
            $res5 = $stmt5->fetch();
            
            $arr['teg'] = $res5['CREBI2TE1'];
            
            return $arr;
        }

        return false;
    }
    
    public function getClientWithId($idClient)
    {
        if ($idClient) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CLIENAETA, CLIENARA1, CLIENARA2, CLIENASRN FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient
                );

            $stmt->execute();
            $res = $stmt->fetch();
            
            if ($res['CLIENAETA'] == 'EIMR' || $res['CLIENAETA'] == 'EIMM' || $res['CLIENAETA'] == 'EIML') {
                $arr['type'] = 'EI';
            } else {
                $arr['type'] = 'PRO';
            }
            
            $arr['rs1'] = $res['CLIENARA1'];
            $arr['rs2'] = $res['CLIENARA2'];
            $arr['siren'] = $res['CLIENASRN'];
            
            // Adresse
            $stmt2 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 WHERE ADRESSNUM = " .
                    $idClient
                );

            $stmt2->execute();
            $res2 = $stmt2->fetch();
            
            $arr['ad1'] = $res2['ADRESSAD1'];
            $arr['ad2'] = $res2['ADRESSAD2'];
            $arr['ad3'] = $res2['ADRESSAD3'];
            $arr['cp'] = $res2['ADRESSCOP'];
            $arr['ville'] = $res2['ADRESSVIL'];
            
            // Forme juridique
            $stmt3 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CLI114007 FROM ZCLI1140, ZCLIENC0 WHERE CLIENCCLI = " . $idClient .
                    " and CLIENCFOR = CLI114003"
                );

            $stmt3->execute();
            $res3 = $stmt3->fetch();
            
            $arr['forme_jur'] = $res3['CLI114007'];
            
            // Capital et naissance
            $stmt4 = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CLIENBCP1 FROM ZCLIENB0 WHERE CLIENBCLI = " . $idClient);

            $stmt4->execute();
            $res4 = $stmt4->fetch();
            
            $arr['capital'] = $res4['CLIENBCP1'];
            
            return $arr;
        }

        return false;
    }
    
    public function getInfosNaissance($idClient)
    {
        if ($idClient) {
            // Date
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CLIENADNA FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient
                );

            $stmt->execute();
            $res = $stmt->fetch();
            
            $date = $res['CLIENADNA'];
            
            $arr['date'] = substr($date, 4, 2) . '/' . substr($date, 2, 2) . '/19' . substr($date, 0, 2);
            
            // Pays, département, ville
            $stmt2 = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CLIENBNAS, CLIENBCOM FROM ZCLIENB0 WHERE CLIENBCLI = " . $idClient
                );

            $stmt2->execute();
            $res2 = $stmt2->fetch();
            
            $arr['ville'] = $res2['CLIENBCOM'];
            
            if (trim($res2['CLIENBNAS']) != 'F' && trim($res2['CLIENBNAS']) != 'FR') {
                $stmt3 = $this->entityManager
                    ->getConnection()
                    ->prepare(
                        "SELECT BAS011008 FROM ZBAS0110, ZCLIENB0 WHERE CLIENBNAS = BAS011004 AND CLIENBCLI = " .
                        $idClient
                    );

                $stmt3->execute();
                $res3 = $stmt3->fetch();
                
                $arr['location'] = $res3['BAS011008'];
            } else {
                $stmt4 = $this->entityManager
                    ->getConnection()
                    ->prepare(
                        "SELECT BAS312003 FROM ZBAS3120, ZCLIENB0 WHERE CLIENBINS = BAS312007 AND CLIENBCLI = " .
                        $idClient
                    );

                $stmt4->execute();
                $res4 = $stmt4->fetch();
                
                if ($res4['BAS312003']) {
                    $arr['location'] = $res4['BAS312003'];
                } else {
                    $stmt5 = $this->entityManager
                        ->getConnection()
                        ->prepare(
                            "SELECT BASLOCDEP FROM ZBASLOC0, ZCLIENB0 WHERE CLIENBINS = BASLOCINS AND CLIENBCLI = " .
                            $idClient
                        );

                    $stmt5->execute();
                    $res5 = $stmt5->fetch();
                    
                    $arr['location'] = $res5['BASLOCDEP'];
                }
            }
            
            return $arr;
        }
        
        return false;
    }
    
    public function getDirigeants($idClient)
    {
        $arr = array();
        
        if ($idClient) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CLIDIRDIR FROM ZCLIDIR0 WHERE CLIDIRCLI = " . $idClient);

            $stmt->execute();
            $res = $stmt->fetchAll();
            
            foreach ($res as $dir) {
                $idClient = $dir['CLIDIRDIR'];
                
                // RS
                $stmt2 = $this->entityManager
                    ->getConnection()
                    ->prepare(
                        "SELECT CLIENAETA, CLIENARA1, CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = ".$dir['CLIDIRDIR']
                    );

                $stmt2->execute();
                $res2 = $stmt2->fetch();
                
                $arr[$idClient]['id'] = $idClient;
                $arr[$idClient]['civ'] = $res2['CLIENAETA'];
                $arr[$idClient]['rs1'] = $res2['CLIENARA1'];
                $arr[$idClient]['rs2'] = $res2['CLIENARA2'];
            }
            
            return $arr;
        }

        return false;
    }
    
    public function getType($id)
    {
        if ($id) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CREPREAUT, CREPREDOS, CREPREPRE FROM ZCREPRE0 WHERE ID = " . $id);

            $stmt->execute();
            $res = $stmt->fetch();
            
            if (trim($res['CREPREAUT']) == 'OUV') {
                return 'OUV';
            } elseif (trim($res['CREPREAUT']) == 'AMO') {
                $stmt2 = $this->entityManager
                    ->getConnection()
                    ->prepare(
                        "SELECT CREPLARTA FROM ZCREPLA0 WHERE CREPLADOS = " . $res['CREPREDOS'] .
                        " AND CREPLAPRE = " . $res['CREPREPRE']
                    );

                $stmt2->execute();
                $res2 = $stmt2->fetch();
                
                if (trim($res2['CREPLARTA']) == '') {
                    return 'TXFIXE';
                } else {
                    return 'TXVAR';
                }
            }
            
            return false;
        }
    }
    
    public function getJourPrelvmt($id)
    {
        if ($id) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CREPREDPE FROM ZCREPRE0 WHERE ID = " . $id);

            $stmt->execute();
            $res = $stmt->fetch();
            
            $date = $res['CREPREDPE'];
            
            if ($date == '0' || $date == '9999999') {
                return null;
            } else {
                return substr($date, -2);
            }
        }
    }
    
    public function getDateDecasmt($numDos, $numPre)
    {
        if ($numDos && $numPre) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT CREMADDMD FROM ZCREMAD0 WHERE CREMADDOS = " . $numDos . " AND CREMADPRE = " . $numPre
                );

            $stmt->execute();
            $res = $stmt->fetch();
            
            $date = $res['CREMADDMD'];
            
            if ($date) {
                return substr($date, 5, 2) . '/' . substr($date, 3, 2) . '/20' . substr($date, 1, 2);
            }
            
            return null;
        }
    }
}
