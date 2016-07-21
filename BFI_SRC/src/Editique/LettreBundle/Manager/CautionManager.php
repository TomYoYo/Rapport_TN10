<?php

namespace Editique\LettreBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Editique\LettreBundle\Entity\Caution;

/**
 * Description of releveManager
 *
 * @author David Briand
 */
class CautionManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public function reinit()
    {
        $this->oCaution = null;
        $this->cautionArray = null;
        $this->template = null;
        $this->doWrite = true;
    }
    
    public function explode($filePath)
    {
        $fileContent = $this->fileManager->lireFichier($filePath);
        $fileContent = $this->cleanContent($fileContent);
        return explode("\f", $fileContent);
    }
    
    public function cleanContent($content)
    {
        return str_replace("\f ", "", $content);
    }
    
    public function lireSpool($spool)
    {
        $this->cautionArray = explode("\n", $spool);
        $this->oCaution = new Caution();
        
        // On parse toutes les lignes
        foreach ($this->cautionArray as $numLine => $line) {
            // On cherche par numéro de ligne
            if ($numLine == 0) {
                $this->oCaution->setIdClientCaution(trim(mb_substr($line, 24, 7, 'utf-8')));
            }
            if ($numLine == 20) {
                $this->oCaution->setIdClientPret(trim(mb_substr($line, 11, 7, 'utf-8')));
                $this->oCaution->setIdCaution(trim(mb_substr($line, 1, 7, 'utf-8')));
            }
            
            if ($numLine == 35) {
                if (trim(mb_substr($line, 1, 6, 'utf-8')) == 'CREDIT') {
                    $this->oCaution->setType('CRE');
                    $this->oCaution->setIdNumDossierPret(trim(mb_substr($line, 34, 9, 'utf-8')));
                    $this->oCaution->setIdDossier(trim(mb_substr($line, 34, 7, 'utf-8')));
                    $this->oCaution->setIdPret(trim(mb_substr($line, 41, 2, 'utf-8')));
                } else {
                    $this->oCaution->setType('ENG');
                    $this->oCaution->setTypeImpression('BANQUE');
                }
            }
        }
    }
    
    public function getDatasFromDB()
    {
        if ($this->oCaution->getType() == 'CRE') {
            $this->setType();
        }
        $this->setRaiSoc();
        $this->setAdresseCaution();
        $this->setAdressePret();
        $this->setDateSituation();
        $this->setCautionDatas();
        
        if ($this->oCaution->getType() == 'CRE_VAR'
            || $this->oCaution->getType() == 'CRE_FIX'
            || $this->oCaution->getType() == 'OUV') {
            $this->setCreditDatas();
            $this->setTaux();
        } else {
            $this->setEngDatas();
        }
    }
    
    private function setType()
    {
        $q = "SELECT CREDOSNCR FROM ZCREDOS0 WHERE CREDOSDOS = " . $this->oCaution->getIdDossier();

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res['CREDOSNCR'] == 'OUV') {
            $this->oCaution->setType('OUV');
            $this->oCaution->setTypeImpression('ANGERS');
        } else {
            $q1 = "SELECT CREPLARTA FROM ZCREPLA0 WHERE CREPLADOS = " . $this->oCaution->getIdDossier();

            $stmt1 = $this->entityManager->getConnection()->prepare($q1);
            $stmt1->execute();
            $res1 = $stmt1->fetch();
            
            if (trim($res1['CREPLARTA']) == null) {
                $this->oCaution->setType('CRE_FIX');
            } else {
                $this->oCaution->setType('CRE_VAR');
            }
        }
    }
    
    private function setRaiSoc()
    {
        // Pour la caution
        $res = $this->getRaiSoc($this->oCaution->getIdClientCaution());
        
        $this->oCaution->setRa1ClientCaution($res['ADRESSRA1']);
        $this->oCaution->setRa2ClientCaution($res['ADRESSRA2']);
        
        // Pour le pret
        $res1 = $this->getRaiSoc($this->oCaution->getIdClientPret());
        
        $this->oCaution->setRa1ClientPret($res1['ADRESSRA1']);
        $this->oCaution->setRa2ClientPret($res1['ADRESSRA2']);
        
        // Check si ok
        $this->checkRaisonSociale();
    }
    
    private function getRaiSoc($idClient)
    {
        $q = "SELECT ADRESSRA1, ADRESSRA2 FROM ZADRESS0 WHERE ADRESSNUM = " . $idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        return $res;
    }
    
    private function checkRaisonSociale()
    {
        if (!trim($this->oCaution->getRa1ClientCaution()) && !trim($this->oCaution->getRa2ClientCaution())) {
            $res = $this->getRaiSoc2($this->oCaution->getIdClientCaution());
            
            $this->oCaution->setRa1ClientCaution($res['CLIENARA1']);
            $this->oCaution->setRa2ClientCaution($res['CLIENARA2']);
        }
        
        if (!trim($this->oCaution->getRa1ClientPret()) && !trim($this->oCaution->getRa1ClientPret())) {
            $res1 = $this->getRaiSoc2($this->oCaution->getIdClientPret());
            
            $this->oCaution->setRa1ClientPret($res1['CLIENARA1']);
            $this->oCaution->setRa2ClientPret($res1['CLIENARA2']);
        }
    }
    
    private function getRaiSoc2($idClient)
    {
        $q = "SELECT CLIENARA1, CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = " . $idClient;

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        return $res;
    }
    
    private function setAdresseCaution()
    {
        $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
            "WHERE ADRESSTYP = 1 AND ADRESSCOA = 'CO' AND ADRESSNUM = " . $this->oCaution->getIdClientCaution();

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        if (!$res) {
            $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
                "WHERE ADRESSTYP = 1 AND ADRESSNUM = " . $this->oCaution->getIdClientCaution();

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
        }
        
        $this->oCaution->setAdresseCaution1($res['ADRESSAD1']);
        $this->oCaution->setAdresseCaution2($res['ADRESSAD2']);
        $this->oCaution->setAdresseCaution3($res['ADRESSAD3']);
        $this->oCaution->setCpCaution($res['ADRESSCOP']);
        $this->oCaution->setVilleCaution($res['ADRESSVIL']);
        
        return $this;
    }
    
    private function setAdressePret()
    {
        $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
            "WHERE ADRESSTYP = 1 AND ADRESSNUM = " . $this->oCaution->getIdClientPret();

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        $this->oCaution->setAdressePret1($res['ADRESSAD1']);
        $this->oCaution->setAdressePret2($res['ADRESSAD2']);
        $this->oCaution->setAdressePret3($res['ADRESSAD3']);
        $this->oCaution->setCpPret($res['ADRESSCOP']);
        $this->oCaution->setVillePret($res['ADRESSVIL']);
        
        return $this;
    }
    
    private function setDateSituation()
    {
        $this->oCaution->setDateSituation(strftime("%d/%m/%Y", mktime(0, 0, 0, 12, 31, date('y') - 1)));
    }
    
    private function setCautionDatas()
    {
        $q = "SELECT DWHOPEMOIN, DWHOPEFIN FROM ZDWHOPE0 WHERE DWHOPENDO = " . $this->oCaution->getIdCaution() .
            " AND DWHOPEDTX = " . $this->transformDateToEng($this->oCaution->getDateSituation());

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res['DWHOPEMOIN'] == 0) {
            $q = "SELECT DWHOPEMOIN, DWHOPEFIN FROM ZDWHOPE0 WHERE DWHOPENDO = " . $this->oCaution->getIdCaution() .
                " AND DWHOPEDTX = " . date('ymd');

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
        }

        // Demande de JLV / JPL
        // Si le montant de la caution est à 0, on mets le document dans le PDF d'anomalie
        if ($res['DWHOPEMOIN'] == 0) {
            $this->oCaution->setTypeImpression('ANOMALIE');
        }
        
        $this->oCaution->setMtCautionInit($res['DWHOPEMOIN']);
        $this->oCaution->setDateFin($res['DWHOPEFIN']);
    }
    
    private function setCreditDatas()
    {
        // Montant initial et restant du
        $q = "SELECT DWHOPEMOIN, DWHOPEMON FROM ZDWHOPE0 WHERE DWHOPENDO = " . $this->oCaution->getIdNumDossierPret() .
            " AND DWHOPEDTX = " . $this->transformDateToEng($this->oCaution->getDateSituation());
        
        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        $this->oCaution->setMtPretInit($res['DWHOPEMOIN']);
        $this->oCaution->setMtPretRestDu($res['DWHOPEMON']);

        // Demande de JLV / JPL
        // Si le montant initial du prêt est à 0, on mets le document dans le PDF d'anomalie
        if ($res['DWHOPEMOIN'] == 0) {
            $this->oCaution->setTypeImpression('ANOMALIE');
        }
        if ($this->oCaution->getType() == 'CRE_VAR' || $this->oCaution->getType() == 'CRE_FIX') {
            if ($res['DWHOPEMOIN'] > 68415) {
                $this->oCaution->setTypeImpression('BANQUE');
            }
        }
        
        // Intérêts
        $q1 = "SELECT SUM(CREBISMIN) AS MTINTERET FROM ZCREBIS0 WHERE CREBISDOS = " . $this->oCaution->getIdDossier() .
            " AND CREBISPRE = " . $this->oCaution->getIdPret() . " AND CREBISTYP = '02'" .
            " AND CREBISEMI > " . $this->transformDateToSab($this->oCaution->getDateSituation());

        $stmt1 = $this->entityManager->getConnection()->prepare($q1);
        $stmt1->execute();
        $res1 = $stmt1->fetch();
        
        $this->oCaution->setMtInteret($res1['MTINTERET']);
    }
    
    private function setTaux()
    {
        if ($this->oCaution->getType() == 'CRE_VAR') {
            // Code et marge
            $q = "SELECT CREPLARTA, CREPLAMAR FROM ZCREPLA0 WHERE CREPLADOS = " . $this->oCaution->getIdDossier() .
                " AND CREPLAPRE = " . $this->oCaution->getIdPret();

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();

            $codeTaux = $res['CREPLARTA'];
            
            $this->oCaution->setMargeTaux($res['CREPLAMAR']);
            
            // Libellé
            $q1 = "SELECT BASTAULIB FROM ZBASTAU0 WHERE BASTAUTAU = '" . $codeTaux ."'";

            $stmt1 = $this->entityManager->getConnection()->prepare($q1);
            $stmt1->execute();
            $res1 = $stmt1->fetch();

            $this->oCaution->setLibCodeTaux($res1['BASTAULIB']);
            
            // Taux
            $q2 = "SELECT BAS025009 FROM ZBAS0250 WHERE BAS025004 = '" . $codeTaux ."'" .
                " AND BAS025005 = " . $this->transformDateToSab($this->oCaution->getDateSituation());

            $stmt2 = $this->entityManager->getConnection()->prepare($q2);
            $stmt2->execute();
            $res2 = $stmt2->fetch();

            // Taux final = marge + taux
            $this->oCaution->setTaux($res['CREPLAMAR'] + $res2['BAS025009']);
        } else {
            $q = "SELECT DWHOPETAU FROM ZDWHOPE0 WHERE DWHOPENDO = " . $this->oCaution->getIdNumDossierPret() .
                " AND DWHOPEDTX = " . $this->transformDateToEng($this->oCaution->getDateSituation());

            $stmt = $this->entityManager->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();

            $this->oCaution->setTaux($res['DWHOPETAU']);
        }
    }
    
    private function setEngDatas()
    {
        // Numéro engagement
        $q = "SELECT DWHOPENDO FROM ZDWHOPE0 WHERE DWHOPECON = " . $this->oCaution->getIdClientPret() .
            " AND DWHOPEOPR = 'CAUENG' AND DWHOPEDTX = " .
            $this->transformDateToEng($this->oCaution->getDateSituation());

        $stmt = $this->entityManager->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();

        if (!$res['DWHOPENDO']) {
            $this->doWrite = false;
            return null;
        }
        
        // Montant engagement
        $q2 = "SELECT DWHOPEMOIN FROM ZDWHOPE0 WHERE DWHOPENDO = " . $res['DWHOPENDO'] .
            " AND DWHOPEDTX = " . $this->transformDateToEng($this->oCaution->getDateSituation());

        $stmt2 = $this->entityManager->getConnection()->prepare($q2);
        $stmt2->execute();
        $res2 = $stmt2->fetch();

        $this->oCaution->setMtEngagementInit($res2['DWHOPEMOIN']);
    }
    
    private function transformDateToSab($date)
    {
        // nous JJ/MM/YYYY
        $annee = substr($date, 8, 2);
        $mois = substr($date, 3, 2);
        $jour = substr($date, 0, 2);

        // SAB : 1AAMMJJ
        return "1" . $annee . $mois . $jour;
    }
    
    private function transformDateToEng($date)
    {
        // nous JJ/MM/YYYY
        $annee = substr($date, 6, 4);
        $mois = substr($date, 3, 2);
        $jour = substr($date, 0, 2);

        // ENG : AAMMJJ
        return $annee . $mois . $jour;
    }
    
    public function ecrireSortie($directory)
    {
        // Choix du template selon type
        switch ($this->oCaution->getType()) {
            case 'CRE_FIX':
                $this->template = __DIR__ . '/../Resources/pdf_template/let_caution_template001.pdf';
                break;
            case 'CRE_VAR':
                $this->template = __DIR__ . '/../Resources/pdf_template/let_caution_template002.pdf';
                break;
            case 'OUV':
                $this->template = __DIR__ . '/../Resources/pdf_template/let_caution_template003.pdf';
                break;
            case 'ENG':
                $this->template = __DIR__ . '/../Resources/pdf_template/let_caution_template004.pdf';
                break;
        }
        
        // PDF
        $pdfFileName = $this->getFileName('pdf');
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF());
        
        // log dans editique
        $this->logEditique(
            $this->oCaution->getIdClientCaution(),
            $this->oCaution->getIdCaution(),
            'caution',
            $directory . $pdfFileName
        );
    }
    
    private function getFileName($ext)
    {
        $fileName = "Avis_Lettre_Caution_" . $this->oCaution->getIdClientCaution() . "_" .
            $this->oCaution->getIdCaution() . "_" . date('Ymd') . "." . $ext;
        return $fileName;
    }
    
    private function getPDF()
    {
        $response = new Response();
        
        $renderTemplate = "EditiqueLettreBundle:Default:lettre_caution.pdf.twig";

        $paramTpl = array(
            'template' => $this->template,
            'caution'  => $this->oCaution
        );
        $this->tplManager->renderResponse($renderTemplate, $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }
    
    public function integrerPourImpression()
    {
        switch ($this->oCaution->getTypeImpression()) {
            case "ANGERS":
                $tabName = "tab_caution_angers";
                break;
            case "BANQUE":
                $tabName = "tab_caution_banque";
                break;
            case "ANOMALIE":
                $tabName = "tab_caution_anomalie";
                break;
        }

        $idClientCaution = $this->oCaution->getIdClientCaution();

        // on init le sous tableau si client pas encore rencontre
        if (!isset($this->{$tabName}[$idClientCaution])) {
            $this->{$tabName}[$idClientCaution] = array($this->oCaution);
        } else {
            $this->{$tabName}[$idClientCaution] []= $this->oCaution;
        }
    }
    
    public function ecrirePDFGlobal($dirSortie)
    {
        if (isset($this->tab_caution_angers)) {
            $fileName = "Avis_Lettre_Caution_ANGERS_" . date('Ymd') . ".pdf";
            $this->buildPDFGlobal($dirSortie, $fileName, $this->tab_caution_angers);
        }
        if (isset($this->tab_caution_banque)) {
            $fileName = "Avis_Lettre_Caution_BANQUE_" . date('Ymd') . ".pdf";
            $this->buildPDFGlobal($dirSortie, $fileName, $this->tab_caution_banque);
        }
        if (isset($this->tab_caution_anomalie)) {
            $fileName = "Avis_Lettre_Caution_ANOMALIE_" . date('Ymd') . ".pdf";
            $this->buildPDFGlobal($dirSortie, $fileName, $this->tab_caution_anomalie);
        }
    }
    
    public function buildPDFGlobal($dirSortie, $fileName, $tab_cautions)
    {
        if (empty($tab_cautions)) {
            return;
        }

        $response = new Response();

        $paramTpl = array(
            'tab_caution_global'    => $tab_cautions,
            'templates'             =>
                array(
                    __DIR__ . '/../Resources/pdf_template/let_caution_template001.pdf',
                    __DIR__ . '/../Resources/pdf_template/let_caution_template002.pdf',
                    __DIR__ . '/../Resources/pdf_template/let_caution_template003.pdf',
                    __DIR__ . '/../Resources/pdf_template/let_caution_template004.pdf'
                )
        );

        $this->tplManager->renderResponse(
            'EditiqueLettreBundle:Default:lettre_caution_global.pdf.twig',
            $paramTpl,
            $response
        );

        $pdf = $this->pdfManager->render($response->getContent());

        $this->fileManager->ecrireFichier($dirSortie, $fileName, $pdf);

        $this->transfertVersServeurFichierDSI($dirSortie . $fileName, 'cautions');
        $this->sendMail('ANGERS', $fileName);
    }
}
