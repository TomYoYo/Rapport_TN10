<?php

namespace Editique\CreditBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Editique\CreditBundle\Entity\SouscriptionEditique;

/**
 * Manager pour la souscription de crédit
 *
 * @author d.briand
 */
class SouscriptionManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $type;
    public $oSouscription;
    public $numPage = "000";
    public $nbPage = "000";
    public $enchainementTpl = array();
    public $tabStartAndLength = array();
    public $nbLignesGarantiesRestantes = 0;
    public $nbGarantiesTraitees = 0;
    public $cursorOperation = 0;
    public $creManager;
    public $entityManager2;
    
    public function reinit()
    {
        $this->type = null;
        $this->oSouscription = null;
        $this->numPage = "000";
        $this->nbPage = "000";
        $this->enchainementTpl = array();
        $this->tabStartAndLength = array();
        $this->nbLignesGarantiesRestantes = 0;
        $this->nbGarantiesTraitees = 0;
        $this->cursorOperation = 0;
        $this->entityManager2 = null;
    }
    
    public function setCreManager($manager)
    {
        $this->creManager = $manager;
    }
    
    public function setEntityManager2($em2)
    {
        $this->entityManager2 = $em2;
    }
    
    public function setContainer($container)
    {
        $this->container = $container;
    }
    
    public function setDatas($id)
    {
        $this->type = $this->creManager->getType($id);
        $souscription = $this->entityManager->getRepository('EditiqueMasterBundle:Souscription')->findOneByIdPret($id);
        $pret = $this->entityManager2->getRepository('EditiqueCreditBundle:Credit')->find($id);
        
        $idClient = $this->creManager->getEmprunteurWithCredit($pret->getNumDos(), $pret->getNumPret());
        
        if (!$idClient) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Une erreur est survenue. Vérifiez le données du crédit sur SAB puis rééssayer.' .
                ' Si le problème persiste, contactez le SI Banque.'
            );
            
            return $this->redirect($this->generateUrl('editique_souscription_credit'));
        }
        
        $plan = $this->creManager->getPlanWithCredit($pret->getNumDos(), $pret->getNumPret());
        $creBis = $this->creManager->getCreBisWithCredit($pret->getNumDos(), $pret->getNumPret());
        $client = $this->creManager->getClientWithId($idClient);
        $client['id'] = $idClient;
        $naissance = $this->creManager->getInfosNaissance($idClient);
        
        if ($client['capital']) {
            $capital = number_format($client['capital'], 2, ',', ' ');
        } else {
            $capital = $souscription->getCapital();
        }
        
        $this->oSouscription = new SouscriptionEditique();
        
        $this->oSouscription
            ->setIdDossier($pret->getNumDos())
            ->setIdPret($pret->getNumPret())
            ->setIdClient($idClient)
            ->setCapital($capital)
            ->setRcs($souscription->getRcs())
            ->setDateNaissance($naissance['date'])
            ->setTypeClient($client['type'])
            ->setDescriptionEi($souscription->getDescriptionEi())
            ->setVilleNaissance($souscription->getVilleNaissance())
            ->setLocalisationNaissance($naissance['location'])
            ->setDirigeants(array($souscription->getDirigeants(), $this->creManager->getDirigeants($idClient)))
            ->setObjetFinancement($souscription->getObjetFin())
            ->setGaranties($souscription->getGaranties())
            ->setAssurance1($souscription->getAss1())
            ->setAssurance2($souscription->getAss2())
            ->setAssurance3($souscription->getAss3())
            ->setAssurance4($souscription->getAss4())
            ->setAssurance5($souscription->getAss5())
            ->setGarantie1($souscription->getGar1())
            ->setGarantie2($souscription->getGar2())
            ->setGarantie3($souscription->getGar3())
            ->setNombreExemplaire($souscription->getNombreExemplaire())
            ->setRaisonSociale1($client['rs1'])
            ->setRaisonSociale2($client['rs2'])
            ->setAdresse1($client['ad1'])
            ->setAdresse2($client['ad2'])
            ->setAdresse3($client['ad3'])
            ->setCodePostal($client['cp'])
            ->setVille($client['ville'])
            ->setFormeJuridique($client['forme_jur'])
            ->setSiren($client['siren'])
            ->setMontantPret($pret->getMontantPret())
            ->setDuree($plan['duree'])
            ->setDtDecaissement($souscription->getDtDec())
            ->setDtDerEcheance($creBis['dtDerEch'])
            ->setCptSupport($creBis['cptSupport'])
            ->setNbEcheances($plan['nbEch'])
            ->setDiffAmo($souscription->getDiffAmo())
            ->setModeAmo($plan['modeAmo'])
            ->setPeriodeInteret($plan['perInt'])
            ->setJourPrelevement($souscription->getJourPre())
            ->setTauxPret($plan['txNomAnn'])
            ->setMargeTauxPret($plan['margeTx'])
            ->setCodeTauxPret($plan['codeTx'])
            ->setLibCodeTaux($plan['libCodeTx'])
            ->setTypeCalcul($souscription->getTypeCalcul())
            ->setFraisDossier($creBis['fraisDos'])
            ->setTeg($creBis['teg'])
            ->setFraisGaranties($souscription->getFraisGar())
            ->setComeng1($souscription->getComeng1())
        ;
    }
    
    public function ecrireSortie($directory)
    {
        $this->nbLignesGarantiesRestantes = $this->compterNbLignesGaranties();
        
        if ($this->type == "TXFIXE") {
            $this->ecrireSouscriptionFixe();
        } elseif ($this->type == "TXVAR") {
            $this->ecrireSouscriptionVariable();
        } elseif ($this->type == "OUV") {
            $this->ecrireOuvertureCredit();
        }
        
        return $this->finaliserEcriture($directory);
    }
    
    public function finaliserEcriture($directory)
    {
        $this->ecritureManager->ecrireNbPage($this->numPage, $this->nbPage);
        $this->oSouscription->nbPage = $this->numPage;

        $this->dateEcriture = date('YmdHis');
        $txt = $this->getFileName('txt');
        $pdf = $this->getFileName('pdf');
        $sortieBrute = implode("\r\n", $this->ecritureManager->content);
        
        // on genere les sorties txt et pdf
        $this->fileManager->ecrireFichier($directory, $txt, $sortieBrute);
        $this->fileManager->ecrireFichier($directory, $pdf, $this->getPDF());

        // log dans editique
        $idClient = $this->oSouscription->getIdClient();
        $idPret = $this->oSouscription->getIdDossier();
        $this->logEditique($idClient, $idPret, 'souscription_credit', $directory . $pdf);

        // transfert vers le serveur de fichier
        //$this->transfertVersServeurFichier($idClient, $directory . $pdf);
        return $pdf;
    }
    
    public function getFileName($ext)
    {
        $idClient = trim($this->oSouscription->getIdClient());
        return
            "Avis_" .
            $idClient .
            "_SOUS_CREDIT_" .
            $this->oSouscription->getIdDossier() .
            "_" .
            $this->dateEcriture .
            "." .
            $ext;
    }
    
    public function getPDF()
    {
        $response = new Response();
        $tpl = $this->getListTpl();
        
        $paramTpl = array(
            'oSouscription'   => $this->oSouscription,
            'tabGaranties'    => $this->oSouscription->getGaranties(),
            'tplArray'        => $tpl,
            'nbPage'          => $this->numPage,
            'enchainementTpl' => $this->enchainementTpl,
            'startAndLength'  => $this->tabStartAndLength,
        );
        $this->tplManager->renderResponse('EditiqueCreditBundle:pdf_sous:souscription.pdf.twig', $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }
    
    public function getListTpl()
    {
        $path = __DIR__ . '/../Resources/pdf_template/';
        $tpl = array();
        
        for ($i = 1; $i <= 33; $i++) {
            if ($i < 10) {
                $val = '0'.$i;
            } else {
                $val = $i;
            }
            
            $tpl['cre'.$val]['pdf'] = $path.'cre_template'.$val.'.pdf';
            $tpl['cre'.$val]['twig'] = 'cre_cond';
        }
        
        $tpl['cre01']['twig'] = 'cre_template01';
        $tpl['cre02']['twig'] = 'cre_template02';
        $tpl['cre10']['twig'] = 'cre_template10';
        $tpl['cre11']['twig'] = 'cre_template11';
        $tpl['cre12']['twig'] = 'cre_template12';
        $tpl['cre13']['twig'] = 'cre_template13';
        $tpl['cre22']['twig'] = 'cre_template22';
        $tpl['cre23']['twig'] = 'cre_template23';
        
        return $tpl;
    }
    
    /**
     * Compte le nombre de ligne ecrites que totaliseront les garanties
     * @return int
     */
    public function compterNbLignesGaranties()
    {
        $res = 0;
        
        foreach ($this->oSouscription->getGaranties() as $gar) {
            $res ++;
        }

        return $res;
    }
    
    public function ecrireSouscriptionFixe()
    {
        $this->getCRE001();
        $this->getCRE002();
        while ($this->nbLignesGarantiesRestantes > 0) {
            $this->getCRE011();
        }
        $this->addGarantiesGenerales();
        for ($i = 3; $i <= 8; $i++) {
            $this->getCreCond('0'.$i);
        }
        $this->getCRE010();
        $this->getCreCond('09');
    }
    
    public function ecrireSouscriptionVariable()
    {
        $this->getCRE012();
        $this->getCRE013();
        while ($this->nbLignesGarantiesRestantes > 0) {
            $this->getCRE011();
        }
        $this->addGarantiesGenerales();
        for ($i = 14; $i <= 20; $i++) {
            $this->getCreCond($i);
        }
        $this->getCRE010();
        $this->getCreCond(21);
    }
    
    public function ecrireOuvertureCredit()
    {
        $this->getCRE022();
        $this->getCRE023();
        while ($this->nbLignesGarantiesRestantes > 0) {
            $this->getCRE011();
        }
        $this->addGarantiesGenerales();
        for ($i = 24; $i <= 29; $i++) {
            $this->getCreCond($i);
        }
        $this->getCRE010();
        $this->getCreCond(30);
    }
    
    public function addGarantiesGenerales()
    {
        if ($this->oSouscription->getGarantie1()) {
            $this->getCreCond(31);
        }
    }
    
    public function getCRE001()
    {
        $this->ecrireCRE00X('01');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getIdDossier(), $this->numLigne++, 3, 7);
        $this->ecrireIdentification();
        $this->ecrireMontantPret();
        $this->ecrireObjetFinancement();
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDuree(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDtDecaissement(), $this->numLigne, 19, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDtDerEcheance(), $this->numLigne, 30, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getCptSupport(), $this->numLigne++, 41, 30);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getNbEcheances(), $this->numLigne, 3, 13);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDiffAmo(), $this->numLigne++, 17, 8);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getModeAmo(), $this->numLigne++, 3, 30);
        
        $this->enchainementTpl [] = 'cre01';
        $this->tabStartAndLength[] = array('start' => 0, 'length' => 0);
    }
    
    public function getCRE002()
    {
        $this->ecrireCRE00X('02');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getTauxPret(), $this->numLigne, 3, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getFraisDossier(), $this->numLigne++, 10, 22);
        $this->ecrireAssurances();
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getTeg(), $this->numLigne, 3, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getFraisGaranties(), $this->numLigne++, 20, 26);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getPeriodeInteret(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getJourPrelevement(), $this->numLigne++, 19, 2);
        $this->ecrireLignesGaranties(25);
    
        $this->enchainementTpl [] = 'cre02';
    }
    
    public function getCRE010()
    {
        $this->ecrireCRE00X('10');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getNombreExemplaire(), $this->numLigne++, 3, 2);
    
        $this->enchainementTpl [] = 'cre10';
        $this->tabStartAndLength[] = array('start' => 0, 'length' => 0);
    }
    
    public function getCRE011()
    {
        $this->ecrireCRE00X('11');
        $this->ecrireLignesGaranties(56);
    
        $this->enchainementTpl [] = 'cre11';
    }
    
    public function getCRE012()
    {
        $this->ecrireCRE00X('12');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getIdDossier(), $this->numLigne++, 3, 7);
        $this->ecrireIdentification();
        $this->ecrireMontantPret();
        $this->ecrireObjetFinancement();
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDuree(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDtDecaissement(), $this->numLigne, 19, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDtDerEcheance(), $this->numLigne, 30, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getCptSupport(), $this->numLigne++, 41, 30);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getNbEcheances(), $this->numLigne, 3, 13);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDiffAmo(), $this->numLigne++, 17, 8);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getModeAmo(), $this->numLigne, 3, 30);
        
        $this->enchainementTpl [] = 'cre12';
        $this->tabStartAndLength[] = array('start' => 0, 'length' => 0);
    }
    
    public function getCRE013()
    {
        $this->ecrireCRE00X('13');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getLibCodeTaux(), $this->numLigne, 3, 30);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getMargeTauxPret(), $this->numLigne++, 34, 13);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getTypeCalcul(), $this->numLigne, 3, 30);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getFraisDossier(), $this->numLigne++, 34, 22);
        $this->ecrireAssurances();
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getTeg(), $this->numLigne, 3, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getFraisGaranties(), $this->numLigne++, 20, 26);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getPeriodeInteret(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getJourPrelevement(), $this->numLigne++, 19, 2);
        $this->ecrireLignesGaranties(29);
    
        $this->enchainementTpl [] = 'cre13';
    }
    
    public function getCRE022()
    {
        $this->ecrireCRE00X('22');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getIdDossier(), $this->numLigne++, 3, 7);
        $this->ecrireIdentification();
        $this->ecrireMontantPret();
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getDuree(), $this->numLigne, 3, 15);
        // A définir quand développé
    
        $this->enchainementTpl [] = 'cre22';
        $this->tabStartAndLength[] = array('start' => 0, 'length' => 0);
    }
    
    public function getCRE023()
    {
        $this->ecrireCRE00X('23');
        $this->ecritureManager->ecrireLigneSortie($this->oSouscription->getFraisGaranties(), $this->numLigne++, 3, 26);
        $this->ecrireLignesGaranties(54);
    
        $this->enchainementTpl [] = 'cre23';
    }
    
    public function getCreCond($i)
    {
        $this->ecrireCRE00X($i);
        
        $this->enchainementTpl [] = 'cre'.$i;
        $this->tabStartAndLength[] = array('start' => 0, 'length' => 0);
    }
    
    public function ecrireCRE00X($i)
    {
        $this->numPage++;
        $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);
        
        $ligne1 = '1CRE0' . $i . '00' . $this->oSouscription->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        
        //NUMERO DE PAGE ET NOMBRE DE PAGE
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 7, 3);
    }
    
    public function ecrireIdentification()
    {
        $identification = $this->getIdentification();
        
        foreach ($identification as $ligne) {
            $this->ecritureManager->ecrireLigneSortie($ligne, $this->numLigne++, 3, 65);
        }
    }
    
    public function ecrireMontantPret()
    {
        $montant = $this->oSouscription->getMontantPret();
        $this->ecritureManager->ecrireLigneSortie($montant[0], $this->numLigne++, 3, 62);
        $this->ecritureManager->ecrireLigneSortie($montant[1], $this->numLigne++, 3, 62);
    }
    
    public function ecrireObjetFinancement()
    {
        $lines = $this->oSouscription->getObjetFinancement();
        
        for ($iterator = 0; $iterator++; $iterator <= 7) {
            if ($lines[$iterator]) {
                $this->ecritureManager->ecrireLigneSortie($lines[$iterator], $this->numLigne++, 3, 69);
            } else {
                $this->ecritureManager->ecrireLigneSortie(" ", $this->numLigne++, 3, 69);
            }
        }
    }
    
    public function ecrireAssurances()
    {
        foreach ($this->oSouscription->getAssurances() as $ligne) {
            $this->ecritureManager->ecrireLigneSortie($ligne, $this->numLigne++, 3, 69);
        }
    }
    
    /**
     * Ecrit les lignes garanties
     * @todo mettre debut et fin pour pagination
     * @return object SouscriptionManager
     */
    public function ecrireLignesGaranties($capaciteMax = 0)
    {
        $tabGar = $this->oSouscription->getGaranties();
        $capaciteRestante = $capaciteMax;
        $iterator = 0;

        while ($capaciteRestante > 0 && isset($tabGar[$this->cursorOperation])) {
            $gar = $tabGar[$this->cursorOperation];

            $this->ecritureManager->ecrireLigneSortie($gar, $this->numLigne++, 3, 69);

            $this->nbLignesGarantiesRestantes--;
            $capaciteRestante--;

            // on incremente le cursor de lecture des operations
            $iterator++;
            $this->cursorOperation++;
        }

        $this->tabStartAndLength[] = array('start' => $this->nbGarantiesTraitees, 'length' => $iterator);
        $this->nbGarantiesTraitees = $this->nbGarantiesTraitees + $iterator;
        return $this;
    }
    
    public function getIdentification()
    {
        if ($this->oSouscription->getTypeClient() == 'PRO') {
            $firstLine = array(
                $this->oSouscription->getRaisonSociale1() . " " .
                $this->oSouscription->getRaisonSociale2() . ","
            );

            $text =
                $this->oSouscription->getFormeJuridique() .
                ", au capital de " .
                $this->oSouscription->getCapital() .
                " EUR, ayant pour numéro SIREN " .
                $this->oSouscription->getSiren() . " " . $this->oSouscription->getRcs() .
                ", ayant son siège sociale au " .
                $this->getAdresse() .
                ", représenté(e) " .
                $this->getListeDirigeants()
            ;

            $textFormate = wordwrap($text, 65, "\r\n");
            $textInTab = explode("\r\n", $textFormate);

            $identification = array_merge($firstLine, $textInTab);
        } else {
            $text =
                $this->oSouscription->getRaisonSociale1() . " " .
                $this->oSouscription->getRaisonSociale2() .
                " né le " . $this->oSouscription->getDateNaissance() .
                " à " . $this->oSouscription->getVilleNaissance().
                " (" . trim($this->oSouscription->getLocalisationNaissance()) . "), " .
                $this->oSouscription->getDescriptionEi() .
                ", ayant pour numéro SIREN " .
                $this->oSouscription->getSiren() . " " . $this->oSouscription->getRcs() .
                ", sise " .
                $this->getAdresse()
            ;
            
            $textFormate = wordwrap($text, 65, "\r\n");
            $identification = explode("\r\n", $textFormate);
        }
        
        
        switch ($nbLines = count($identification)) {
            case 2:
            case 4:
            case 6:
            case 8:
                for ($i=0; $i < (10 - $nbLines)/2; $i++) {
                    array_unshift($identification, "");
                    array_push($identification, "");
                }
                break;
            case 3:
            case 5:
            case 7:
            case 9:
                for ($i=0; $i < (9 - $nbLines)/2; $i++) {
                    array_unshift($identification, "");
                    array_push($identification, "");
                }
                array_push($identification, "");
                break;
        }
        
        $this->oSouscription->setIdentification($identification);
        
        return $identification;
    }
    
    public function getAdresse()
    {
        $adresse = $this->oSouscription->getAdresse1() . ', ';
        
        if ($this->oSouscription->getAdresse2()) {
            $adresse .= $this->oSouscription->getAdresse2() . ', ';
        }
        if ($this->oSouscription->getAdresse3()) {
            $adresse .= $this->oSouscription->getAdresse3() . ', ';
        }
        
        $adresse .= $this->oSouscription->getCodePostal() . ' ' . $this->oSouscription->getVille();
        
        return $adresse;
    }
    
    public function getListeDirigeants()
    {
        $tables = $this->oSouscription->getDirigeants();
        $qualites = $tables[0];
        $dirigeants = $tables[1];
        
        $text = "";
        $i = 1;
        
        foreach ($dirigeants as $dirigeant) {
            $qualite = $qualites[$dirigeant['id']];
            
            if ($i == count($dirigeants)) {
                $text .= " et ";
            } elseif ($i != 1) {
                $text .= ", ";
            }
            
            $text .= "par " . trim($dirigeant['civ']) . " " . trim($dirigeant['rs1']) . " " . trim($dirigeant['rs2']);
            $text .= ", agissant en qualité";
            
            if ($this->firstCharIsVowel($qualite)) {
                $text .= ' d\'';
            } else {
                $text .= ' de ';
            }
            
            $text .= $qualite;
            
            if ($i == count($dirigeants)) {
                $text .= ".";
            }
            
            $i++;
        }
        
        return $text;
    }
    
    private function firstCharIsVowel($str)
    {
        $firstChar = substr($str, 0, 1);
        $firstChar = strtr(
            $firstChar,
            'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ',
            'AAAAAACEEEEEIIIINOOOOOUUUUY'
        );
        $firstChar = strtr(
            $firstChar,
            'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ',
            'aaaaaaceeeeiiiinooooouuuuyy'
        );
        
        if ($firstChar == 'a' || $firstChar == 'A') {
            return true;
        }
        if ($firstChar == 'e' || $firstChar == 'E') {
            return true;
        }
        if ($firstChar == 'i' || $firstChar == 'I') {
            return true;
        }
        if ($firstChar == 'o' || $firstChar == 'O') {
            return true;
        }
        if ($firstChar == 'u' || $firstChar == 'U') {
            return true;
        }
        if ($firstChar == 'y' || $firstChar == 'Y') {
            return true;
        }
        if ($firstChar == 'h' || $firstChar == 'H') {
            return true;
        }
        
        return false;
    }
}
