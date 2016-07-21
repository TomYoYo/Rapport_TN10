<?php

namespace Editique\FiscalBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Editique\FiscalBundle\Entity\Compteur;

/**
 * Manager pour la souscription de crédit
 *
 * @author d.briand
 */
class IFUManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public function init()
    {
        $this->ecritureManager->content = array();
        $this->atLeastOneTotal = false;
    }
    
    public function getIFU()
    {
        $foyers = $this->entityManager
            ->getRepository('Editique\FiscalBundle\Entity\Foyer')
            ->findByAnnee(date("Y", strtotime("-1 year")));
        
        if ($foyers === null) {
            return false;
        }

        return $foyers;
    }
    
    public function setFoyer($foyer)
    {
        $this->oFoyer = $foyer;
    }
    
    public function setAdmin()
    {
        $this->oAdmin = $this->entityManager
            ->getRepository('Editique\FiscalBundle\Entity\Admin')
            ->findOneBy(
                array('numFiscal' => $this->oFoyer->getNumFiscal(), 'annee' => date("Y", strtotime("-1 year")))
            );

        if ($this->oAdmin) {
            $this->oFoyer->setAdmin($this->oAdmin);
            return true;
        } else {
            return false;
        }
    }
    
    public function setTotaux()
    {
        $this->oCompteur = new Compteur();

        // STEP 1 : Init des compteurs
        $this->initCompteurs(array(
            'PCA82',
            'PCA84',
            'DATISO',
            'DATINS',
            'ECHISO',
            'ECHINS',
            'DATRSO',
            'ECHRSO',
            'DATPRL',
            'ECHPRE'
        ));

        //STEP 2 : Attribution des compteurs aux champs

        //IFMPV = Information Facultative : Montant des Plus Values
        //MTCVM = Montant Total des Cessions de Valeurs Immobilières
        $this->oCompteur->setIfmpv($this->roundNull($this->PCA84));
        $this->oCompteur->setTr2($this->roundNull($this->DATISO + $this->DATINS + $this->ECHISO + $this->ECHINS));
        $this->oCompteur->setBh2($this->roundNull($this->DATRSO + $this->ECHRSO));
        $this->oCompteur->setCk2($this->roundNull($this->DATPRL + $this->ECHPRE));
        $this->oCompteur->setVg3($this->roundNull($this->PCA84));
        $this->oCompteur->setMtcvm($this->roundNull($this->PCA82));
        
        $this->oFoyer->setCompteur($this->oCompteur);

        if ($this->atLeastOneTotal) {
            return true;
        }

        return false;
    }

    private function initCompteurs($lst_cpt)
    {
        foreach ($lst_cpt as $cpt) {
            $this->$cpt = $this->getTotal($cpt);
        }
    }
    
    private function roundNull($val)
    {
        if ($val) {
            $val_rounded = number_format(round($val), 0, ',', ' ');
            
            if ($val_rounded != 0) {
                return $val_rounded;
            }
        }
        
        return null;
    }
    
    private function getTotal($compteur)
    {
        // On cherche dans la table de modifications
        $total_cpt = $this->sqlResult(
            'SUM(IFUHCTMOT)',
            'ZIFUHCT0',
            array(
                'IFUHCTCPT' => "'" . $compteur . "'",
                'IFUHCTCLI' => $this->oFoyer->getNumFiscal(),
                'IFUHCTANF' => date("Y", strtotime("-1 year"))
            )
        );

        // S'il n'y a pas eu de modification, on cherche dans la table initiale
        if ($total_cpt == 0) {
            $total_cpt = $this->sqlResult(
                'SUM(IFUAGRMON)',
                'ZIFUAGR0',
                array(
                    'IFUAGRCPT' => "'" . $compteur . "'",
                    'IFUAGRFOY' => $this->oFoyer->getNumFiscal(),
                    'IFUAGRANE' => date("Y", strtotime("-1 year"))
                )
            );
        }

        if ($total_cpt > 0) {
            $this->atLeastOneTotal = true;
        }

        return $total_cpt;
    }
    
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
    
    public function ecrireSortie($directory)
    {
        $this->numLigne = 1;

        //$this->initIFU('001')
        //    ->writeData();

        $this->contentSortie = $this->ecritureManager->getContent();

        $sortieBrute = implode("\r\n", $this->contentSortie);

        $pdfFileName = $this->getFileName('pdf');

        // Ecriture du fichier txt
        //$this->fileManager->ecrireFichier($directory, $this->getFileName('txt'), $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF());

        // log dans editique
        $this->logEditique(
            $this->oFoyer->getNumFiscal(),
            $this->oFoyer->getNumCompte(),
            'ifu',
            $directory . $pdfFileName
        );

        // transfert vers le serveur de fichier
        //$this->transfertVersServeurFichier($this->oFoyer->getIdClient(), $directory . $pdfFileName);

        return $sortieBrute;
    }
    
    public function integrerPourImpression()
    {
        $idFoyer = $this->oFoyer->getNumFiscal();

        // on init le sous tableau si client pas encore rencontre
        $this->tab_releve_print[$idFoyer] = $this->oFoyer;
    }
    
    public function ecrirePDFGlobal($dirSortie)
    {
        $fileName = "IFU_" . $this->year . "_IMPRESSION" . ".pdf";
        $this->buildPDFGlobal($dirSortie, $fileName);
    }
    
    public function buildPDFGlobal($dirSortie, $fileName)
    {
        if (empty($this->tab_releve_print)) {
            return;
        }

        $response = new Response();

        $paramTpl = array(
            'tab_releve_global' => $this->tab_releve_print,
            'template_let' => __DIR__ . '/../Resources/pdf_template/tplIFU000.pdf',
            'template' => __DIR__ . '/../Resources/pdf_template/tplIFU001.pdf',
            'year'     => $this->year
        );

        $this->tplManager->renderResponse('EditiqueFiscalBundle:Default:IFUGlobal.pdf.twig', $paramTpl, $response);

        $pdf = $this->pdfManager->render($response->getContent());

        $this->fileManager->ecrireFichier($dirSortie, $fileName, $pdf);

        //$this->transfertVersServeurFichierDSI($dirSortie . $fileName, 'releve');
        //$this->sendMail('ANGERS');
    }
    
    public function getPDF()
    {
        $response = new Response();
        $this->tplManager->renderResponse(
            'EditiqueFiscalBundle:Default:ifu.pdf.twig',
            array(
                'template_let' => __DIR__ . '/../Resources/pdf_template/tplIFU000.pdf',
                'template' => __DIR__ . '/../Resources/pdf_template/tplIFU001.pdf',
                'foyer'    => $this->oFoyer,
                'year'     => $this->year
            ),
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext)
    {
        $this->year = date('Y', mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1));
        
        $fileName = "IFU_" .
            $this->year .
            "_" .
            $this->oFoyer->getNumFiscal() .
            "_" .
            date('Ymd') .
            "." .
            $ext;
        return $fileName;
    }
    
    private function initIFU($number)
    {
        $string = '1IFU' . $number . '00' . $this->oFoyer->getNumFiscal();
        $this->ecritureManager->ecrireLigneSortie($string, $this->numLigne++, 1, 16);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }
    
    private function writeData()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getNom(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getPrenom(), $this->numLigne++, 37, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getAdresse1(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getAdresse2(), $this->numLigne++, 37, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getAdresse3(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getAdresse4(), $this->numLigne++, 37, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oFoyer->getNumCompte(), $this->numLigne, 3, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getDateNaissance(), $this->numLigne, 12, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getSiret(), $this->numLigne, 21, 14);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getCommuneNaissance(), $this->numLigne++, 37, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getNomCommuneNaissance(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getDepartementNaissance(), $this->numLigne++, 37, 2);
        $this->ecritureManager->ecrireLigneSortie($this->oAdmin->getNomMarital(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getIfmpv(), $this->numLigne++, 37, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getEe21(), $this->numLigne, 3, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getEe22(), $this->numLigne, 22, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getDc2(), $this->numLigne++, 41, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getTr2(), $this->numLigne, 3, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getBh2(), $this->numLigne, 22, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getCa2(), $this->numLigne++, 41, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getVg3(), $this->numLigne, 3, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getVh3(), $this->numLigne, 22, 17);
        $this->ecritureManager->ecrireLigneSortie($this->oCompteur->getMtcvm(), $this->numLigne++, 41, 17);

        return $this;
    }
}
