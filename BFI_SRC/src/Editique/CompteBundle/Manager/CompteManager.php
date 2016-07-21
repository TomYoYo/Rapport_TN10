<?php

namespace Editique\CompteBundle\Manager;

use Editique\CompteBundle\Entity\Compte;
use Editique\CompteBundle\Entity\Representant;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of compteManager
 *
 * @author d.briand
 */
class CompteManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $oCompte = null;
    public $listeSouscriptions = array();
    public $idClient = null;
    public $nbRepresentant = 0;
    public $compteArray = null;
    public $oRepresentants = array();
    
    // REINIT
    public function reinit()
    {
        $this->oCompte = null;
        $this->listeSouscriptions = array();
        $this->idClient = null;
        $this->nbRepresentant = 0;
        $this->compteArray = null;
        $this->oRepresentants = array();
    }
    
    // LECTURE
    public function readFile($filePath)
    {
        @$xml = simplexml_load_file($filePath);
        if ($xml) {
            $this->compteArray = json_decode(json_encode(simplexml_load_file($filePath)), true);
            
            $list = $this->compteArray['body']['lstsouscription']['souscription'];
            
            if (isset($list[0])) {
                $this->listeSouscriptions = $this->compteArray['body']['lstsouscription']['souscription'];
            } else {
                $this->listeSouscriptions[] = $this->compteArray['body']['lstsouscription']['souscription'];
            }
        } else {
            $this->addLog(
                'Le fichier XML (' . $filePath . ') est vide ou comporte une/plusieurs erreur(s).',
                'Lecture du XML en entrée',
                'fatal'
            );
        }
    }
    
    public function initForTrigger($idLigne)
    {
        $this->oCompte = new Compte();
        
        $ligne = $this->sqlResult(array('BAGPASNUM', 'BAGPASPAS'), 'ZBAGPAS0', array('id' => $idLigne));
        $this->oCompte->setMdpEsab($ligne['BAGPASPAS']);
        
        $this->idClient =
            $this->sqlResult('BAGGCOCLI', 'ZBAGGCO0, ZBAGPAS0', array('ZBAGGCO0.BAGGCONUM' => $ligne['BAGPASNUM']));
        $this->oCompte->setIdClient($this->idClient);
        $this->oCompte->setNumCompte(0);
        
        // Raison sociale
        $req = $this->entityManager
            ->getConnection()
            ->prepare(
                "SELECT ADRESSRA1, ADRESSRA2 FROM ZADRESS0" .
                " WHERE ADRESSNUM LIKE '%".$this->idClient."%' AND ADRESSCOA = 'CO'"
            );

        $req->execute();
        $raisonSociale = $req->fetch();
        
        if (!$raisonSociale) {
            $req = $this->entityManager
                ->getConnection()
                ->prepare(
                    "SELECT ADRESSRA1, ADRESSRA2 FROM ZADRESS0" .
                    " WHERE ADRESSNUM LIKE '%".$this->idClient."%'"
                );
            
            $req->execute();
            $raisonSociale = $req->fetch();
        }
        
        $this->oCompte->setRaisonSociale1(trim($raisonSociale['ADRESSRA1']));
        $this->oCompte->setRaisonSociale2(trim($raisonSociale['ADRESSRA2']));
        
        $this->checkRaisonSociale();
        
        $this->setAdresse();
        
        return $this;
    }
    
    private function checkRaisonSociale()
    {
        if (!$this->oCompte->getRaisonSociale1() && !$this->oCompte->getRaisonSociale2()) {
            $raisonSociale =
                $this->sqlResult(array('CLIENARA1', 'CLIENARA2'), 'ZCLIENA0', array('CLIENACLI' => $this->idClient));
            
            $this->oCompte->setRaisonSociale1($raisonSociale['CLIENARA1']);
            $this->oCompte->setRaisonSociale2($raisonSociale['CLIENARA2']);
        }
        
        return $this;
    }
    
    private function setAdresse()
    {
        $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
            "WHERE ADRESSTYP = 1 AND ADRESSCOA = 'CO' AND ADRESSNUM = " . $this->idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        if (!$res) {
            $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
                "WHERE ADRESSTYP = 1 AND ADRESSNUM = " . $this->idClient;

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
        }
        
        $this->oCompte->setAdrCourrier1($res['ADRESSAD1']);
        $this->oCompte->setAdrCourrier2($res['ADRESSAD2']);
        $this->oCompte->setAdrCourrier3($res['ADRESSAD3']);
        $this->oCompte->setAdrCourrierCP($res['ADRESSCOP']);
        $this->oCompte->setAdrCourrierVil($res['ADRESSVIL']);
        
        return $this;
    }
    
    public function setData($souscription)
    {
        $this->oCompte = new Compte();
        $this->idClient = $souscription['numCli'];
        
        $this->setCommonData($souscription);
        
        if ($this->oCompte->getCodProSou() == 'PRP') {
            $this->nbRepresentant = $this->countTitulaire($souscription);
            for ($i = 0; $i < $this->countTitulaire($souscription); $i++) {
                $this->setOCompteTitulaire($souscription, $i);
            }
        } elseif ($this->oCompte->getCodProSou() == 'PRO' || $this->oCompte->getCodProSou() == 'ENT') {
            $this->nbRepresentant = $this->countResponsable();
            for ($i = 1; $i < $this->countResponsable() + 1; $i++) {
                $this->setOCompteResponsable($i);
            }
        } elseif ($this->oCompte->getCodEdi() == 'VALIDA'
            || $this->oCompte->getCodEdi() == 'CREATI'
            || $this->oCompte->getCodEdi() == 'AVENAN') {
            $this->setOCompteLettre();
        }
    }
    
    private function setCommonData($souscription)
    {
        $this->oCompte->setCodEve($souscription['codEve']);
        $this->oCompte->setCodEdi($souscription['codEdi']);
        $this->oCompte->setCodEta($souscription['codEta']);
        $this->oCompte->setCodProSou($souscription['codProSou']);
        $codeEtat = $souscription['codEta'];
        if ($codeEtat == 'EIMR' || $codeEtat == 'EIMM' || $codeEtat == 'EIML') {
            $rs = $this->getRaisonSocialeEI();
        } else {
            $rs = null;
        }
        if (!$rs) {
            $this->oCompte->setRaisonSociale1($souscription['rais1']);
            $this->oCompte->setRaisonSociale2($souscription['rais2']);
        } else {
            $this->oCompte->setRaisonSociale1($rs['rais1']);
            $this->oCompte->setRaisonSociale2($rs['rais2']);
        }
        $this->oCompte->setFormeJuridique($this->getFromTitulaire($souscription, 'libFjuTit'));
        $this->oCompte->setSiren($this->getFromTitulaire($souscription, 'sirenTit'));
        $this->oCompte->setAdrSiege1($souscription['adr1']);
        $this->oCompte->setAdrSiege2($souscription['adr2']);
        $this->oCompte->setAdrSiege3($souscription['adr3']);
        $this->oCompte->setAdrSiegeCP($souscription['codPos']);
        $this->oCompte->setAdrSiegeVil($souscription['ville']);
        $this->oCompte->setTelSociete(
            isset($souscription['telBur']) ? $souscription['telBur'] : $this->setTelBur()
        );
        $this->oCompte->setEmailSociete($souscription['email']);
        $this->oCompte->setIdClient($this->idClient);
        $this->oCompte->setNumCompte($souscription['cptSou']);
        $this->setAdresseCourrier();
        $this->getTitle();
        
        $fax = $this->sqlResult(
            'CLINTANUT',
            'ZCLINTA0',
            array('CLINTANUM' => $this->idClient, 'CLINTATYN' => '\'FAX\'')
        );
        
        $catTiers = $this->sqlResult('CLIENACAT', 'ZCLIENA0', array('CLIENACLI' => $this->idClient));
        
        $req = $this->entityManager
            ->getConnection()
            ->prepare(
                "SELECT ECHTASMT1 FROM ZECHTAS0, ZPLAN0, ZCOMPTE0, ZCLIENA0" .
                " WHERE ECHTASCR1 = PLANCOPRO AND ECHTASCR2 = CLIENACAT AND ECHTASCMI LIKE '%ICR%'" .
                " AND CLIENACLI = '$this->idClient' AND PLANCOOBL = COMPTEOBL" .
                " AND COMPTECOM = '".$this->oCompte->getNumCompte()."'"
            );

        $req->execute();
        $res = $req->fetch();
        
        $txCompte = $res['ECHTASMT1'];
        
        $this->oCompte->setFaxSociete($fax);
        $this->oCompte->setCatTiers($catTiers);
        $this->oCompte->setTauxCompte($txCompte);
    }
    
    public function setOCompteTitulaire($souscription, $step = 0)
    {
        $titulaire = new Representant();
        
        $titulaire->setIdResp($this->getFromTitulaire($souscription, 'numTit', $step));
        $titulaire->setCivilite($this->getFromTitulaire($souscription, 'etaTit', $step));
        $titulaire->setNom($this->getFromTitulaire($souscription, 'rais1Tit', $step));
        $titulaire->setNomJF($this->getFromTitulaire($souscription, 'filTit', $step));
        $titulaire->setPrenom($this->getFromTitulaire($souscription, 'rais2Tit', $step));
        $titulaire->setDateNai($this->getFromTitulaire($souscription, 'datNaiTit', $step));
        $titulaire->setVilleNai($this->getFromTitulaire($souscription, 'comNaiTit', $step));
        $titulaire->setNationalite($this->getFromTitulaire($souscription, 'payNatTit', $step));
        $titulaire->setAdr1($this->getFromTitulaire($souscription, 'adres1Tit', $step));
        $titulaire->setAdr2($this->getFromTitulaire($souscription, 'adres2Tit', $step));
        $titulaire->setAdr3($this->getFromTitulaire($souscription, 'adres3Tit', $step));
        $titulaire->setCodPos($this->getFromTitulaire($souscription, 'codPosTit', $step));
        $titulaire->setVil($this->getFromTitulaire($souscription, 'vilTit', $step));
        $titulaire->setTelFixe($this->getFromTitulaire($souscription, 'telDomTit', $step));
        $titulaire->setTelPort($this->getFromTitulaire($souscription, 'telPorTit', $step));
        $titulaire->setEmail($this->getFromTitulaire($souscription, 'emailTit', $step));
        
        $regMat = $this->sqlResult(
            'BAS119007',
            'ZCLIENC0, ZBAS1190',
            array('CLIENCRGM' => 'BAS119003', 'CLIENCCLI' => $this->getFromTitulaire($souscription, 'numTit', $step))
        );
        
        $titulaire->setRegimeMat($regMat);
        
        $this->oRepresentants[] = $titulaire;
    }
    
    public function setOCompteResponsable($step = 1)
    {
        $responsable = new Representant();
        
        $idResp = $this->sqlResult(
            'CLIDIRDIR',
            "(SELECT rownum r, CLIDIRDIR FROM ZCLIDIR0 WHERE CLIDIRCLI = '" . $this->idClient ."')",
            array('r' => $step)
        );
        
        if (!$idResp) {
            $this->addLog(
                'Aucun responsable n\'est relié au numéro client donné',
                'Récupération des informations responsable',
                'fatal'
            );
            
            return false;
        }
        
        $denomination = $this->sqlResult(
            array('CLIENAETA', 'CLIENARA1', 'CLIENAFIL', 'CLIENARA2', 'CLIENADNA'),
            'ZCLIENA0',
            array('CLIENACLI' => $idResp)
        );
        
        $nationalite = $this->sqlResult(
            'BAS011008',
            'ZCLIENA0, ZBAS0110',
            array('CLIENACLI' => $idResp, 'CLIENANAT' => 'BAS011004')
        );
        
        // PEUT ËTRE UN LIKE ?
        $adresses = $this->sqlResult(
            array('ADRESSAD1', 'ADRESSAD2', 'ADRESSAD3', 'ADRESSCOP', 'ADRESSVIL'),
            'ZADRESS0',
            array('ADRESSNUM' => $idResp)
        );
        
        $regMat = $this->sqlResult(
            'BAS119007',
            'ZCLIENC0, ZBAS1190',
            array('CLIENCCLI' => $idResp, 'CLIENCRGM' => 'BAS119003')
        );
        
        $villeNai = $this->sqlResult('CLIENBCOM', 'ZCLIENB0', array('CLIENBCLI' => $idResp));
        $fixe = $this->sqlResult('CLINTANUT', 'ZCLINTA0', array('CLINTANUM' => $idResp, 'CLINTATYN' => '\'DOMIC.\''));
        $mobile = $this->sqlResult('CLINTANUT', 'ZCLINTA0', array('CLINTANUM' => $idResp, 'CLINTATYN' => '\'MOBILE\''));
        $email = $this->sqlResult('CLINTANUT', 'ZCLINTA0', array('CLINTANUM' => $idResp, 'CLINTATYN' => '\'EMAILP\''));
        
        $responsable->setIdResp($idResp);
        $responsable->setCivilite($denomination['CLIENAETA']);
        $responsable->setNom($denomination['CLIENARA1']);
        $responsable->setNomJF($denomination['CLIENAFIL']);
        $responsable->setPrenom($denomination['CLIENARA2']);
        $responsable->setDateNai($denomination['CLIENADNA']);
        $responsable->setVilleNai($villeNai);
        $responsable->setNationalite($nationalite);
        $responsable->setAdr1($adresses['ADRESSAD1']);
        $responsable->setAdr2($adresses['ADRESSAD2']);
        $responsable->setAdr3($adresses['ADRESSAD3']);
        $responsable->setCodPos($adresses['ADRESSCOP']);
        $responsable->setVil($adresses['ADRESSVIL']);
        $responsable->setRegimeMat($regMat);
        $responsable->setTelFixe($fixe);
        $responsable->setTelPort($mobile);
        $responsable->setEmail($email);
        
        $this->oRepresentants[] = $responsable;
    }
    
    private function setOCompteLettre()
    {
        $idEsab = $this->sqlResult('BAGGCOCAB', 'ZBAGGCO0', array('BAGGCOCLI' => $this->idClient));
        $MdpEsab = $this->sqlResult(
            'BAGPASPAS',
            'ZBAGPAS0, ZBAGGCO0',
            array('BAGPASABO' => 'BAGGCOCAB', 'BAGGCOCLI' => $this->idClient)
        );
        
        $this->oCompte->setIdEsab($idEsab);
        $this->oCompte->setMdpEsab($MdpEsab);
    }
    
    private function setAdresseCourrier()
    {
        $adresses = $this->sqlResult(
            array('ADRESSAD1', 'ADRESSAD2', 'ADRESSAD3', 'ADRESSCOP', 'ADRESSVIL'),
            'ZADRESS0',
            array('ADRESSTYP' => 1, 'ADRESSCOA' => '\'CO\'', 'ADRESSNUM' => $this->oCompte->getIdClient())
        );
        
        $this->oCompte->setAdrCourrier1($adresses['ADRESSAD1']);
        $this->oCompte->setAdrCourrier2($adresses['ADRESSAD2']);
        $this->oCompte->setAdrCourrier3($adresses['ADRESSAD3']);
        $this->oCompte->setAdrCourrierCP($adresses['ADRESSCOP']);
        $this->oCompte->setAdrCourrierVil($adresses['ADRESSVIL']);
    }
    
    private function setTelBur()
    {
        $tel =
            $this->sqlResult(
                'CLINTANUT',
                'ZCLINTA0',
                array('CLINTANUM' => $this->idClient, 'CLINTATYN' => '\'DOMIC.\'')
            );
    
        if (!$tel['CLINTANUT']) {
            $tel =
                $this->sqlResult(
                    'CLINTANUT',
                    'ZCLINTA0',
                    array('CLINTANUM' => $this->idClient, 'CLINTATYN' => '\'BUREAU\'')
                );
        }
        
        $this->oCompte->setTelSociete($tel['CLINTANUT']);
    }
    
    private function getFromTitulaire($souscription, $balise, $i = 0)
    {
        if (isset($souscription['lstTit']['titulaire'][$i])) {
            $content = $souscription['lstTit']['titulaire'][$i][$balise];
        } else {
            $content = $souscription['lstTit']['titulaire'][$balise];
        }
        
        if (is_array($content)) {
            return null;
        }
        
        return $content;
    }
    
    // Accesseurs
    public function getTitle()
    {
        if ($this->oCompte->getCodProSou() == "PRO"
            || $this->oCompte->getCodProSou() == "PRP") {
            $typeCpt = "PROFESSIONNEL";
        } elseif ($this->oCompte->getCodProSou() == "ENT") {
            $typeCpt = "ENTREPRISE";
        } else {
            $typeCpt = "";
        }
        
        if ($this->oCompte->getCodEdi() == "AVENAN") {
            $typeEdit = "AVENANT";
        } else {
            $typeEdit = "SOUSCRIPTION";
        }
        
        $title = $typeEdit . " D'UN COMPTE COURANT " . $typeCpt;
        $this->oCompte->setTitle($title);
        return $this->ecritureManager->centrerEspace($title, 46);
    }
    
    public function getRaisonSocialeEI()
    {
        $req = $this->entityManager
            ->getConnection()
            ->prepare(
                "SELECT CLIENCNOM, CLIENCPRE FROM ZCLIENC0 WHERE CLIENCCLI = '" .
                $this->idClient .
                "'"
            );

        $req->execute();
        $res = $req->fetch();
        
        return array('rais1' => $res['CLIENCPRE'], 'rais2' => $res['CLIENCNOM']);
    }
    
    public function getContentSortie()
    {
        return $this->contentSortie;
    }
    
    private function countTitulaire($souscription)
    {
        if (isset($souscription['lstTit']['titulaire'][0])) {
            return count($souscription['lstTit']['titulaire']);
        } else {
            return 1;
        }
    }
    
    private function countResponsable()
    {
        $req = $this->entityManager
            ->getConnection()
            ->prepare(
                "SELECT COUNT(CLIDIRDIR) AS COUNT FROM ZCLIDIR0 WHERE CLIDIRCLI = '" .
                $this->idClient .
                "'"
            );

        $req->execute();
        $res = $req->fetch();
        
        return $res['COUNT'];
    }
    
    // ECRITURE
    public function ecrireSortie($directory, $type)
    {
        $this->numLigne = 1;

        switch ($type) {
            case 'souscription':
                $this->getSCC002();
        
                foreach ($this->oRepresentants as $representant) {
                    $this->getSCC001($representant)
                        ->getSCC003();
                }
                break;
            case 'club':
                $this->getLET001();
                break;
            case 'mdp':
                $this->getLET002();
                break;
        }

        $this->contentSortie = $this->ecritureManager->getContent();

        $sortieBrute = implode("\r\n", $this->contentSortie);

        $txtFileName = $this->getFileName('txt', $type);
        $pdfFileName = $this->getFileName('pdf', $type);

        // Ecriture du fichier txt
        //$this->fileManager->ecrireFichier($directory, $txtFileName, $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF($type));

        // transfert vers le serveur de fichier sauf si MDP
        if ($type == 'souscription' || $type == 'club') {
            // log dans editique
            $this->logEditique(
                $this->oCompte->getIdClient(),
                $this->oCompte->getNumCompte(),
                $type,
                $directory . $pdfFileName
            );
            $this->transfertVersServeurFichier($this->oCompte->getIdClient(), $directory . $pdfFileName);
        } elseif ($type == 'mdp') {
            $this->transfertVersServeurFichierDSI($directory . $pdfFileName, 'esab');
            $this->sendMail('ANGERS', $pdfFileName);
        }
        
        return array($txtFileName, $pdfFileName);
    }
    
    private function getSCC001($representant)
    {
        $this->initCompte('001')
            ->writeContent($representant);
        
        return $this;
    }
    
    private function getSCC002()
    {
        $this->initCompte('002')
            ->writeHeader();
        
        return $this;
    }
    
    private function getSCC003()
    {
        $this->initCompte('003');
        
        return $this;
    }
    
    private function getLET001()
    {
        $this
            ->initLettre('001')
            ->writeGeneratalLetter();
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getTauxCompte(), $this->numLigne, 14, 8);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getNumCompte(), $this->numLigne, 23, 20);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getIdEsab(), $this->numLigne++, 44, 12);
    }
    
    private function getLET002()
    {
        $this
            ->initLettre('002')
            ->writeGeneratalLetter(true);
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne++, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getmdpEsab(), $this->numLigne++, 3, 64);
    }
    
    private function initCompte($modelNumber)
    {
        $string = '1SCC' . $modelNumber . '00' . $this->oCompte->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($string, $this->numLigne++, 1, 16);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }
    
    private function initLettre($modelNumber)
    {
        $string = '1LET' . $modelNumber . '00' . $this->oCompte->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($string, $this->numLigne++, 1, 16);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }
    
    private function writeGeneratalLetter($ligneVide = false)
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getRaisonSociale(), $this->numLigne++, 3, 65);
        
        if ($ligneVide) {
            $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        }
        
        $adrSiege = $this->oCompte->getAdresseSiege();
        $this->ecritureManager->ecrireLigneSortie($adrSiege[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[3], $this->numLigne++, 3, 45);
    }

    private function writeContent($representant)
    {
        $this->ecritureManager->ecrireLigneSortie($this->getTitle(), $this->numLigne++, 3, 46);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getRaisonSociale(), $this->numLigne++, 3, 65);
        
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getFormeJuridique(), $this->numLigne, 3, 4);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getSiren(), $this->numLigne, 8, 9);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getIdClient(), $this->numLigne, 18, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getNumCompte(), $this->numLigne++, 26, 20);
        
        $adrSiege = $this->oCompte->getAdresseSiege();
        $this->ecritureManager->ecrireLigneSortie($adrSiege[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[3], $this->numLigne++, 3, 45);
        
        $adrCourrier = $this->oCompte->getAdresseCourrier();
        $this->ecritureManager->ecrireLigneSortie($adrCourrier[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrCourrier[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrCourrier[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrCourrier[3], $this->numLigne++, 3, 45);
        
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getTelSociete(), $this->numLigne++, 3, 50);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getEmailSociete(), $this->numLigne++, 3, 50);
        
        $noms = $representant->getNom();
        $this->ecritureManager->ecrireLigneSortie($noms[0], $this->numLigne++, 3, 60);
        $this->ecritureManager->ecrireLigneSortie($noms[1], $this->numLigne, 3, 12);
        $this->ecritureManager->ecrireLigneSortie($representant->getPrenom(), $this->numLigne, 17, 32);
        $this->ecritureManager->ecrireLigneSortie($representant->getCivilite(), $this->numLigne++, 50, 4);
        
        $this->ecritureManager->ecrireLigneSortie($representant->getDateNai(), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($representant->getVilleNai(), $this->numLigne++, 14, 32);
        
        $this->ecritureManager->ecrireLigneSortie($representant->getNationalite(), $this->numLigne++, 3, 30);
        
        $adresseClient = $representant->getAdresseClient();
        $this->ecritureManager->ecrireLigneSortie($adresseClient[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresseClient[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresseClient[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresseClient[3], $this->numLigne++, 3, 45);
        
        $this->ecritureManager->ecrireLigneSortie($representant->getRegimeMat(), $this->numLigne++, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($representant->getTelFixe(), $this->numLigne++, 3, 50);
        $this->ecritureManager->ecrireLigneSortie($representant->getTelPort(), $this->numLigne++, 3, 50);
        $this->ecritureManager->ecrireLigneSortie($representant->getEmail(), $this->numLigne++, 3, 50);

        return $this;
    }
    
    private function writeHeader()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getRaisonSociale(), $this->numLigne++, 3, 65);
        
        $adrSiege = $this->oCompte->getAdresseSiege();
        $this->ecritureManager->ecrireLigneSortie($adrSiege[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adrSiege[3], $this->numLigne++, 3, 45);
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oCompte->getTauxCompte(), $this->numLigne++, 14, 8);
    }
    
    public function getPDF($type)
    {
        $response = new Response();
        $templates = $this->getListTemplates($type);
        
        if ($type == 'souscription') {
            $layout = 'EditiqueCompteBundle:Default:compte.pdf.twig';
        } elseif ($type == 'club') {
            $layout = 'EditiqueCompteBundle:Default:lettre_club.pdf.twig';
        } elseif ($type == 'mdp') {
            $layout = 'EditiqueCompteBundle:Default:lettre_mot_de_passe.pdf.twig';
        }
        
        $this->tplManager->renderResponse(
            $layout,
            array(
                'templates'     => $templates,
                'compte'        => $this->oCompte,
                'representants' => $this->oRepresentants
            ),
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }
    
    // VERIFICATIONS
    public function doSouscription()
    {
        if ($this->oCompte->getCodEve() != 'VALSOU') {
            return false;
        }

        if ($this->oCompte->getCodEta() != 'VALIDE') {
            return false;
        }
        
        if ($this->oCompte->getCodProSou() != 'PRO'
            && $this->oCompte->getCodProSou() != 'PRP'
            && $this->oCompte->getCodProSou() != 'ENT') {
            return false;
        }
        
        if ($this->oCompte->getCatTiers() != 'CLB') {
            return false;
        }
        
        return true;
    }
    
    public function doSouscriptionLetter()
    {
        if ($this->oCompte->getCodEve() != 'VALSOU') {
            return false;
        }

        if ($this->oCompte->getCodEta() != 'VALIDE') {
            return false;
        }

        if ($this->oCompte->getCodProSou() != 'PROCLB'
            && $this->oCompte->getCodProSou() != 'PRPCLB'
            && $this->oCompte->getCodProSou() != 'ENTCLB') {
            return false;
        }

        if ($this->oCompte->getCodEdi() != 'AVENAN'
            && $this->oCompte->getCodEdi() != 'VALIDA'
            && $this->oCompte->getCodEdi() != 'CREATI') {
            return false;
        }
        
        return true;
    }
    
    public function doMdpLetter()
    {
        if ($this->oCompte->getCodEve() != 'VALSOU') {
            return false;
        }

        if ($this->oCompte->getCodEta() != 'VALIDE') {
            return false;
        }

        if ($this->oCompte->getCodProSou() != 'PROCLB'
            && $this->oCompte->getCodProSou() != 'PRPCLB'
            && $this->oCompte->getCodProSou() != 'ENTCLB'
            && $this->oCompte->getCodProSou() != '003'
            && $this->oCompte->getCodProSou() != '004'
            && $this->oCompte->getCodProSou() != '005'
            && $this->oCompte->getCodProSou() != '006'
            && $this->oCompte->getCodProSou() != '007') {
            return false;
        }

        if ($this->oCompte->getCodEdi() != 'AVENAN'
            && $this->oCompte->getCodEdi() != 'VALIDA'
            && $this->oCompte->getCodEdi() != 'CREATI') {
            return false;
        }
        
        return true;
    }
    
    // FONCTIONS GENERALES
    private function sqlResult($column, $table, $whereParameters)
    {
        if (is_array($column)) {
            $query = "SELECT " . implode(',', $column) . " FROM " . $table;
        } else {
            $query = "SELECT " . $column . " FROM " . $table;
        }
        
        $i = 0;
        
        foreach ($whereParameters as $parameter => $value) {
            if ($i == 0) {
                $query .= ' WHERE ';
            } else {
                $query .= ' AND ';
            }
            
            $query .= $parameter . ' = ' . $value;
            $i++;
        }
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        
        if (is_array($column)) {
            return $req->fetch();
        }
        
        $res = $req->fetch();
        
        return $res[$column];
    }
    
    public function getFileName($ext, $type)
    {
        switch ($type) {
            case 'souscription':
                $txt = 'SouscriptionCompteCourant';
                break;
            case 'club':
                $txt = 'LettreSouscriptionClub';
                break;
            case 'mdp':
                $txt = 'LettreMdpEsab';
                break;
        }
        
        $fileName = "Avis_" .
            $this->oCompte->getIdClient() .
            "_" .
            $txt .
            "_" .
            $this->oCompte->getNumCompte() .
            "_" .
            date('Ymd') .
            "." .
            $ext;
        return $fileName;
    }
    
    private function getListTemplates($type)
    {
        switch ($type) {
            case 'souscription':
                return array(
                    '1' => __DIR__ . '/../Resources/pdf_template/scc_template001.pdf',
                    '2' => __DIR__ . '/../Resources/pdf_template/scc_template002.pdf',
                    '3' => __DIR__ . '/../Resources/pdf_template/scc_template003.pdf',
                );
            case 'club':
                return array(
                    '1' => __DIR__ . '/../Resources/pdf_template/let_template001.pdf',
                );
            case 'mdp':
                return array(
                    '1' => __DIR__ . '/../Resources/pdf_template/let_template002.pdf',
                );
        }
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
