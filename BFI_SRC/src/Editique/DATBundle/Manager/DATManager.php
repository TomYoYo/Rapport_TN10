<?php

namespace Editique\DATBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of releveManager
 *
 * @author jd labails
 */
class DATManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $error = array();
    public $fatalError = false;
    public $datArray = array();
    public $oDat = null;
    public $contentSortie = array();
    public $templates = array();
    public $numLigne = 1;
    public $idClient = '0';
    public $accountNumber = '';

    public function getODat()
    {
        return $this->oDat;
    }

    public function initODat($idDat)
    {
        $this->oDat = $this->entityManager
            ->getRepository('Editique\DATBundle\Entity\Dat')
            ->findOneById($idDat);

        if ($this->oDat === null) {
            return false;
        }

        $tabPeriodeTaux = $this->entityManager
            ->getRepository('Editique\DATBundle\Entity\PeriodeTaux')
            ->findByNumOpe($this->oDat->getNumOpe(), array('num' => 'ASC'));

        $this->oDat->setPeriodeTaux($tabPeriodeTaux);

        $siren = $this->sqlResult('CLIENASRN', 'ZCLIENA0', array('CLIENACLI' => $this->oDat->getIdClient()));
        if (!$siren) {
            // On prend le RCS
            $siren = $this->sqlResult('CLIENCREG', 'ZCLIENC0', array('CLIENCCLI' => $this->oDat->getIdClient()));
        }
        $this->oDat->setSiren($siren);
        
        $this->setNomPrenom();
    }
    
    private function setNomPrenom()
    {
        $idDirigeant = $this->sqlResult('CLIDIRDIR', 'ZCLIDIR0', array('CLIDIRCLI' => $this->oDat->getIdClient()));
        
        if ($idDirigeant) {
            $column = array('CLIENARA1', 'CLIENARA2');
            $where = array('CLIENACLI' => $idDirigeant);
            $res = $this->sqlResult($column, 'ZCLIENA0', $where);
            $this->oDat->setNomRepresentant($res['CLIENARA1']);
            $this->oDat->setPrenomRepresentant($res['CLIENARA2']);
        } else {
            $civilite = $this->sqlResult('CLIENCNOM', 'ZCLIENC0', array('CLIENCCLI' => $this->oDat->getIdClient()));
            
            if (trim($civilite)) {
                $column = array('CLIENCPRE', 'CLIENCNOM');
                $where = array('CLIENCCLI' => $this->oDat->getIdClient());
                $res = $this->sqlResult($column, 'ZCLIENC0', $where);
                $this->oDat->setNomRepresentant($res['CLIENCNOM']);
                $this->oDat->setPrenomRepresentant($res['CLIENCPRE']);
            } else {
                $column = array('CLIENARA1', 'CLIENARA2');
                $where = array('CLIENACLI' => $this->oDat->getIdClient());
                $res = $this->sqlResult($column, 'ZCLIENA0', $where);
                $this->oDat->setNomRepresentant($res['CLIENARA1']);
                $this->oDat->setPrenomRepresentant($res['CLIENARA2']);
            }
        }
    }

    public function ecrireSortie($directory, $idDat)
    {
        $this->numLigne = 1;

        $this->initODat($idDat);

        if (!$this->oDat->isDATFixe() && !$this->oDat->isDATProgressif()) {
            $this->logManager->addError(
                'Le DAT n\'est ni fixe ni progressif'
            );
            return false;
        }

        if ($this->oDat->isDATFixe()) {
            $this->ecrireDAT001();
        }
        if ($this->oDat->isDATProgressif()) {
            $this->ecrireDAT003();
        }

        $this->ecrireDAT00X(2);

        $this->templates = array(
            'pageFixe' => __DIR__ . '/../Resources/pdf_template/dat_Fixe.pdf',
            'pageCondition' => __DIR__ . '/../Resources/pdf_template/dat_Condition.pdf',
            'pageProgressif' => __DIR__ . '/../Resources/pdf_template/dat_Progressif.pdf',
        );

        $this->contentSortie = $this->ecritureManager->getContent();

        $sortieBrute = implode("\r\n", $this->contentSortie);

        $pdfFileName = $this->getFileName('pdf');

        // Ecriture du fichier txt
        $this->fileManager->ecrireFichier($directory, $this->getFileName('txt'), $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF());

        // log dans editique
        $idClient = $this->oDat->getIdClient();
        $numCpt = $this->oDat->getNumCompteSupport();
        $this->logEditique($idClient, $numCpt, 'dat', $directory . $pdfFileName);

        // transfert vers le serveur de fichier
        $this->transfertVersServeurFichier($idClient, $directory . $pdfFileName);

        return $pdfFileName;
    }

    public function getPDF()
    {
        $response = new Response();
        $this->tplManager->renderResponse(
            'EditiqueDATBundle:Default:dat.pdf.twig',
            array(
                'templates' => $this->templates,
                'dat'       => $this->getODat()
            ),
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext)
    {
        $fileName = "Avis_" .
            trim($this->oDat->getIdClient()) .
            "_DAT_" .
            trim($this->oDat->getNumCompteSupport()) .
            "_" .
            date('Ymd_His') .
            "."
            . $ext;
        return $fileName;
    }

    public function ecrireDAT001()
    {
        $this->ecrireDAT00X(1);
        $this->ecrireDAT();

        $this->ecritureManager->ecrireLigneSortie($this->oDat->getNumCompteSupport(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireDateFromSAB($this->oDat->getDateEdition(), $this->numLigne, 24, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getTauxNominal(), $this->numLigne, 35, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getTauxActuriel(), $this->numLigne, 42, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getSiren(), $this->numLigne++, 49, 9);

        $representant = trim($this->oDat->getNomRepresentant()).' '.trim($this->oDat->getNomRepresentant());
        $this->ecritureManager->ecrireLigneSortie($representant, $this->numLigne++, 3, 65);
    }

    public function ecrireDAT003()
    {
        $this->ecrireDAT00X(3);
        $this->ecrireDAT();

        $this->ecritureManager->ecrireLigneSortie($this->oDat->getNumCompteSupport(), $this->numLigne++, 3, 20);

        $tabPeriode = $this->oDat->getPeriodeTaux();
        foreach ($tabPeriode as $num => $pt) {
            $ligne = 'Période ' . $num . ' du ';

            if ($num < 10) {
                $ligne .= ' ';
            }

            $ligne .= $pt->getDateDebut() . ' au ' . $pt->getDatefin();
            $txNominalProgressif = $pt->getTaux() . '%';

            if ($num%2 == 0) {
                $this->ecritureManager->ecrireLigneSortie($ligne, $this->numLigne, 33, 22);
                $this->ecritureManager->ecrireLigneSortie($txNominalProgressif, $this->numLigne++, 56, 6);
            } else {
                $this->ecritureManager->ecrireLigneSortie($ligne, $this->numLigne, 3, 22);
                $this->ecritureManager->ecrireLigneSortie($txNominalProgressif, $this->numLigne, 26, 6);
            }

            if ($num == 10) {
                break; // on en met pas plus
            }
        }
        if (count($tabPeriode) < 10) {
            for ($i = count($tabPeriode); $i < 10; $i++) {
                if ($i%2 == 0) {
                    $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne, 33, 22);
                    $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++, 56, 6);
                } else {
                    $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne, 3, 22);
                    $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne, 26, 6);
                }
            }
        }

        $this->ecritureManager->ecrireDateFromSAB($this->oDat->getDateEdition(), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getTauxActuriel(), $this->numLigne, 14, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getSiren(), $this->numLigne++, 21, 9);

        $representant = trim($this->oDat->getNomRepresentant()).' '.trim($this->oDat->getNomRepresentant());
        $this->ecritureManager->ecrireLigneSortie($representant, $this->numLigne++, 3, 65);

    }

    public function ecrireDAT00X($i)
    {
        $ligne1 = '1DAT00' . $i . '00' . $this->oDat->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
    }

    /**
     * Mutualisation des parties DAT communes aux maquettes 001 et 003
     **/
    public function ecrireDAT()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oDat->getIdClient(), $this->numLigne++, 3, 7);

        $tit = trim($this->oDat->getNom()) . ' ' . trim($this->oDat->getPrenom());
        $this->ecritureManager->ecrireLigneSortie($tit, $this->numLigne++, 3, 65);

        $adresseFinale=$this->oDat->getAdresseFinale();
        $this->ecritureManager->ecrireLigneSortie($adresseFinale[0], $this->numLigne++, 3, 39);
        $this->ecritureManager->ecrireLigneSortie($adresseFinale[1], $this->numLigne++, 3, 39);
        $this->ecritureManager->ecrireLigneSortie($adresseFinale[2], $this->numLigne++, 3, 39);
        $this->ecritureManager->ecrireLigneSortie($adresseFinale[3], $this->numLigne++, 3, 39);
        $this->ecritureManager->ecrireLigneSortie($adresseFinale[4], $this->numLigne++, 3, 39);

        $this->ecritureManager->ecrireLigneSortie($this->oDat->getRefDat(), $this->numLigne, 3, 21);
        $this->ecritureManager->ecrireDateFromSAB($this->oDat->getDateDebut(), $this->numLigne, 25);
        $this->ecritureManager->ecrireDateFromSAB($this->oDat->getDateEcheance(), $this->numLigne++, 36);

        $montantDepot = $this->oDat->getMontantDepotFinal();
        $this->ecritureManager->ecrireLigneSortie($montantDepot[0], $this->numLigne++, 3, 69);
        $this->ecritureManager->ecrireLigneSortie($montantDepot[1], $this->numLigne++, 3, 69);
    }

    /**
     *
     * @param type $column peut etre un tableau ou un scalaire
     * @param type $table
     * @param type $whereParameters
     * @return type tableau ou scalaire selon le type de la variable column
     */
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
}
