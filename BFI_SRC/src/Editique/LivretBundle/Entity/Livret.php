<?php

namespace Editique\LivretBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livret
 *
 * @ORM\Table(name="ZERECOU0")
 * @ORM\Entity
 */
class Livret
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * /////ORM\Column(name="idClient", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="ETAT1", type="string", length=4)
     */
    private $codeEtatSouscripteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM11", type="string", length=32)
     */
    private $raisonSociale1;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM12", type="string", length=32)
     */
    private $raisonSociale2;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRE11", type="string", length=38)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRE21", type="string", length=38)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRE31", type="string", length=38)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRE41", type="string", length=38)
     */
    private $adresse4;

    /**
     * @var string
     *
     * @ORM\Column(name="COPOS1", type="string", length=12)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="VILLE1", type="string", length=32)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODUIT", type="string", length=3)
     */
    private $typeCptERE;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPT1", type="string", length=20)
     */
    private $numCptERE;

    /**
     * @var string
     *
     * @ORM\Column(name="MONT101", type="string", length=15)
     */
    private $montantInitial;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPT2", type="string", length=20)
     */
    private $compteSupport;

    /**
     * @var string
     *
     * @ORM\Column(name="TAUX1", type="string", length=9)
     */
    private $taux;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPT3", type="string", length=20)
     */
    private $comptePreleve;

    /**
     * @var string
     *
     * @ORM\Column(name="CARAC1", type="string", length=1)
     */
    private $periodicite;

    /**
     * @var string
     *
     * @ORM\Column(name="MONT118", type="string", length=15)
     */
    private $montantPeriodique;

    /**
     * @var string
     *
     * @ORM\Column(name="NBR03", type="string", length=3)
     */
    private $jourVersement;

    /**
     * @var string
     *
     * @ORM\Column(name="DATECOUR", type="string", length=10)
     */
    private $dateSouscription;

    /**
     * @var string
     *
     * @ORM\Column(name="ERECOUCOU", type="string", length=6)
     */
    private $typeCourrier;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     * @return Livret
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set codeEtatSouscripteur
     *
     * @param string $codeEtatSouscripteur
     * @return Livret
     */
    public function setCodeEtatSouscripteur($codeEtatSouscripteur)
    {
        $this->codeEtatSouscripteur = $codeEtatSouscripteur;

        return $this;
    }

    /**
     * Get codeEtatSouscripteur
     *
     * @return string
     */
    public function getCodeEtatSouscripteur()
    {
        return $this->codeEtatSouscripteur;
    }

    /**
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     * @return Livret
     */
    public function setRaisonSociale1($raisonSociale1)
    {
        $this->raisonSociale1 = $raisonSociale1;

        return $this;
    }

    /**
     * Get raisonSociale1
     *
     * @return string
     */
    public function getRaisonSociale1()
    {
        return $this->raisonSociale1;
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     * @return Livret
     */
    public function setRaisonSociale2($raisonSociale2)
    {
        $this->raisonSociale2 = $raisonSociale2;

        return $this;
    }

    /**
     * Get raisonSociale2
     *
     * @return string
     */
    public function getRaisonSociale2()
    {
        return $this->raisonSociale2;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     * @return Livret
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     * @return Livret
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     * @return Livret
     */
    public function setAdresse3($adresse3)
    {
        $this->adresse3 = $adresse3;

        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string
     */
    public function getAdresse3()
    {
        return $this->adresse3;
    }

    /**
     * Set adresse4
     *
     * @param string $adresse4
     * @return Livret
     */
    public function setAdresse4($adresse4)
    {
        $this->adresse4 = $adresse4;

        return $this;
    }

    /**
     * Get adresse4
     *
     * @return string
     */
    public function getAdresse4()
    {
        return $this->adresse4;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     * @return Livret
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Livret
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set typeCptERE
     *
     * @param string $typeCptERE
     * @return Livret
     */
    public function setTypeCptERE($typeCptERE)
    {
        $this->typeCptERE = $typeCptERE;

        return $this;
    }

    /**
     * Get typeCptERE
     *
     * @return string
     */
    public function getTypeCptERE()
    {
        return $this->typeCptERE;
    }

    /**
     * Set numCptERE
     *
     * @param string $numCptERE
     * @return Livret
     */
    public function setNumCptERE($numCptERE)
    {
        $this->numCptERE = $numCptERE;

        return $this;
    }

    /**
     * Get numCptERE
     *
     * @return string
     */
    public function getNumCptERE()
    {
        return $this->numCptERE;
    }

    /**
     * Set montantInitial
     *
     * @param string $montantInitial
     * @return Livret
     */
    public function setMontantInitial($montantInitial)
    {
        $this->montantInitial = $montantInitial;

        return $this;
    }

    /**
     * Get montantInitial
     *
     * @return string
     */
    public function getMontantInitial()
    {
        return $this->montantInitial;
    }

    /**
     * Set compteSupport
     *
     * @param string $compteSupport
     * @return Livret
     */
    public function setCompteSupport($compteSupport)
    {
        $this->compteSupport = $compteSupport;

        return $this;
    }

    /**
     * Get compteSupport
     *
     * @return string
     */
    public function getCompteSupport()
    {
        return $this->compteSupport;
    }

    /**
     * Set taux
     *
     * @param string $taux
     * @return Livret
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get taux
     *
     * @return string
     */
    public function getTaux()
    {
        return $this->taux;
    }

    /**
     * Set comptePreleve
     *
     * @param string $comptePreleve
     * @return Livret
     */
    public function setComptePreleve($comptePreleve)
    {
        $this->comptePreleve = $comptePreleve;

        return $this;
    }

    /**
     * Get comptePreleve
     *
     * @return string
     */
    public function getComptePreleve()
    {
        return $this->comptePreleve;
    }

    /**
     * Set periodicite
     *
     * @param string $periodicite
     * @return Livret
     */
    public function setPeriodicite($periodicite)
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    /**
     * Get periodicite
     *
     * @return string
     */
    public function getPeriodicite()
    {
        return $this->periodicite;
    }

    /**
     * Set montantPeriodique
     *
     * @param string $montantPeriodique
     * @return Livret
     */
    public function setMontantPeriodique($montantPeriodique)
    {
        $this->montantPeriodique = $montantPeriodique;

        return $this;
    }

    /**
     * Get montantPeriodique
     *
     * @return string
     */
    public function getMontantPeriodique()
    {
        $res = $this->montantPeriodique;
        if ((float) $this->montantPeriodique > 0) {
            $res .= ' euros';
        }
        return $res;
    }

    /**
     * Set jourVersement
     *
     * @param string $jourVersement
     * @return Livret
     */
    public function setJourVersement($jourVersement)
    {
        $this->jourVersement = $jourVersement;

        return $this;
    }

    /**
     * Get jourVersement
     *
     * @return string
     */
    public function getJourVersement()
    {
        return $this->jourVersement;
    }

    /**
     * Set dateSouscription
     *
     * @param string $dateSouscription
     * @return Livret
     */
    public function setDateSouscription($dateSouscription)
    {
        $this->dateSouscription = $dateSouscription;

        return $this;
    }

    /**
     * Get dateSouscription
     *
     * @return string
     */
    public function getDateSouscription()
    {
        return $this->dateSouscription;
    }

    /**
     * Set typeCourrier
     *
     * @param string $typeCourrier
     * @return Livret
     */
    public function setTypeCourrier($typeCourrier)
    {
        $this->typeCourrier = $typeCourrier;

        return $this;
    }

    /**
     * Get typeCourrier
     *
     * @return string
     */
    public function getTypeCourrier()
    {
        return $this->typeCourrier;
    }

    public function getMontantFinal()
    {
        $montant = trim(number_format((float) str_replace('.', '', $this->montantInitial), 2, ',', ' ')) .
            ' euros (' .
            \BackOffice\ParserBundle\Manager\EcritureManager::asLetters((float) $this->montantInitial) .
            ' euros)'
        ;
        return array(
            substr($montant, 0, 69),
            substr($montant, 69, 69)
        );
    }

    public function isParticulier()
    {
        if ($this->codeEtatSouscripteur === 'MR  ' ||
            $this->codeEtatSouscripteur === 'MME ' ||
            $this->codeEtatSouscripteur === 'MLLE' ||
            $this->codeEtatSouscripteur === 'INDI'
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdresseFinale()
    {
        $adresse = trim($this->adresse1) .
            trim($this->adresse2) .
            trim($this->adresse3) .
            trim($this->adresse4)
        ;
        return array(
            substr($adresse, 0, 69),
            substr($adresse, 69, 69),
            substr($adresse, 138, 23),
        );
    }

    public function getVilleFinale()
    {
        return trim($this->codePostal) . ' ' . trim($this->ville);
    }

    public function isLVA()
    {
        return $this->typeCptERE === 'LVA';
    }

    public function isLDD()
    {
        return $this->typeCptERE === 'LDD';
    }

    public function isCSL()
    {
        return $this->typeCptERE === 'CSL';
    }

    public function getVersementPeriodiqueO()
    {
        if ((float) $this->montantPeriodique > 0) {
            return 'X';
        } else {
            return ' ';
        }
    }

    public function getVersementPeriodiqueN()
    {
        if ((float) $this->montantPeriodique == 0) {
            return 'X';
        } else {
            return ' ';
        }
    }

    public function getLibPeriodicite()
    {
        if ((float) $this->montantPeriodique > 0) {
            switch ($this->periodicite) {
                case 'H':
                    return 'Hebdomadaire';
                case 'M':
                    return 'Mensuelle';
                case 'T':
                    return 'Trimestrielle';
                case 'S':
                    return 'Semestrielle';
                default:
                    return ' ';
            }
        } else {
            return ' ';
        }
    }
}
