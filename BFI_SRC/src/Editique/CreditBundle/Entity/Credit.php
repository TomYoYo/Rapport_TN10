<?php

namespace Editique\CreditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Credit
 *
 * @ORM\Table(name="ZCREPRE0")
 * @ORM\Entity(repositoryClass="Editique\CreditBundle\Entity\CreditRepository")
 */
class Credit
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
     * @ORM\Column(name="CREPREPRE", type="string", length=6)
     */
    private $numPret;

    /**
     * @var string
     *
     * @ORM\Column(name="CREPREDOS", type="string", length=7)
     */
    private $numDos;

    /**
     * @var string
     *
     * @ORM\Column(name="CREPREMON", type="string", length=18)
     */
    private $montantPret;

    /**
     * @var string
     *
     * /////ORM\Column(name="idClient", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * /////ORM\Column(name="raisonSocial1", type="string", length=32)
     */
    private $raisonSocial1;

    /**
     * @var string
     *
     * /////ORM\Column(name="raisonSocial2", type="string", length=32)
     */
    private $raisonSocial2;

    /**
     * @var string
     *
     * /////ORM\Column(name="adresse1", type="string", length=38)
     */
    private $adresse1;

    /**
     * @var string
     *
     * /////ORM\Column(name="adresse2", type="string", length=38)
     */
    private $adresse2;

    /**
     * @var string
     *
     * /////ORM\Column(name="adresse3", type="string", length=38)
     */
    private $adresse3;

    /**
     * @var string
     *
     * /////ORM\Column(name="codePostal", type="string", length=12)
     */
    private $codePostal;

    /**
     * @var string
     *
     * /////ORM\Column(name="ville", type="string", length=32)
     */
    private $ville;

    /**
     * @var string
     *
     * /////ORM\Column(name="taeg", type="string", length=24)
     */
    private $taeg;

    /**
     * //ORM\OneToMany(targetEntity="Echeance", mappedBy="credit")
     */
    private $echeances;

    /**
     * //ORM\Column( type="float")
     */
    private $montantCapital = 0;

    /**
     * //ORM\Column( type="float")
     */
    private $totalInteret = 0;


    /**
     * //ORM\Column( type="float")
     */
    private $totalHorsAssurance = 0;


    /**
     * //ORM\Column( type="float")
     */
    private $totalAssurance = 0;


    /**
     * //ORM\Column( type="float")
     */
    private $totalPaye = 0;

    /**
     * //ORM\Column( type="integer")
     */
    private $duree = 0;

    /**
     * //ORM\Column( type="string")
     */
    private $periodicite = 'M';

    /**
     * //ORM\Column( type="string")
     */
    private $typeTaux;
    
    /**
     * //ORM\Column( type="string")
     */
    private $codeTaux;
    
    /**
     * //ORM\Column( type="float")
     */
    private $taux = 0;

    /**
     * //ORM\Column( type="float")
     */
    private $margeTaux = 0;
    
    /**
     * //ORM\Column( type="string")
     */
    private $titre;
    /**
     * //ORM\Column( type="string")
     */
    private $dateEdition;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->echeances = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set numPret
     *
     * @param string $numPret
     *
     * @return Credit
     */
    public function setNumPret($numPret)
    {
        $this->numPret = $numPret;

        return $this;
    }

    /**
     * Get numPret
     *
     * @return string
     */
    public function getNumPret()
    {
        return $this->numPret;
    }

    /**
     * Set numDos
     *
     * @param string $numDos
     *
     * @return Credit
     */
    public function setNumDos($numDos)
    {
        $this->numDos = $numDos;

        return $this;
    }

    /**
     * Get numDos
     *
     * @return string
     */
    public function getNumDos()
    {
        return $this->numDos;
    }

    /**
     * Set montantPret
     *
     * @param string $montantPret
     *
     * @return Credit
     */
    public function setMontantPret($montantPret)
    {
        $this->montantPret = $montantPret;

        return $this;
    }

    /**
     * Get montantPret
     *
     * @return string
     */
    public function getMontantPret($final = true)
    {
        if (trim($this->montantPret)) {
            if ($final) {
                return $this->montantPret . ' euros';
            } else {
                return $this->montantPret;
            }
        }
        
        return null;
    }

    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getIdClient()
    {
        return $this->idClient;
    }

    public function setRaisonSocial1($raisonSocial)
    {
        $this->raisonSocial1 = $raisonSocial;

        return $this;
    }

    public function getRaisonSocial1()
    {
        return $this->raisonSocial1;
    }

    public function setRaisonSocial2($raisonSocial)
    {
        $this->raisonSocial2 = $raisonSocial;

        return $this;
    }

    public function getRaisonSocial2()
    {
        return $this->raisonSocial2;
    }

    public function getDenomitationComplete()
    {
        return $this->raisonSocial1." ".$this->raisonSocial2 ;
    }

    public function setAdresse1($adresse)
    {
        $this->adresse1 = $adresse;

        return $this;
    }

    public function getAdresse1()
    {
        return $this->adresse1;
    }

    public function setAdresse2($adresse)
    {
        $this->adresse2 = $adresse;

        return $this;
    }

    public function getAdresse2()
    {
        return $this->adresse2;
    }

    public function setAdresse3($adresse)
    {
        $this->adresse3 = $adresse;

        return $this;
    }

    public function getAdresse3()
    {
        return $this->adresse3;
    }

    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getCodePostal()
    {
        return $this->codePostal;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getAdresseFinale()
    {
        if ($this->adresse1) {
            $adresseFinale[] = $this->adresse1;
        }
        if ($this->adresse2) {
            $adresseFinale[] = $this->adresse2;
        }
        if ($this->adresse3) {
            $adresseFinale[] = $this->adresse3;
        }
        if ($this->adresse4) {
            $adresseFinale[] = $this->adresse4;
        }
        $adresseFinale[] = $this->codePostal." ".$this->ville;

        return $adresseFinale;
    }

    public function setTAEG($taeg)
    {
        $this->taeg = $taeg;

        return $this;
    }

    public function getTAEG()
    {
        if (trim($this->taeg)) {
            return number_format($this->taeg, 2, ',', '') . '%';
        }
        
        return null;
    }

    public function getNumCredit()
    {
        return $this->numDos." - ".$this->numPret;
    }

    /**
     * Add echeance
     *
     * @param \Editique\CreditBundle\Entity\Echeance $echeance
     * @return Credit
     */
    public function addEcheance(\Editique\CreditBundle\Entity\Echeance $echeance)
    {
        $this->echeances[] = $echeance;
        return $this;
    }

    /**
     * Remove echeance
     *
     * @param \Editique\CreditBundle\Entity\Echeance $echeance
     */
    public function removeEcheance(\Editique\CreditBundle\Entity\Echeance $echeance)
    {
        $this->echeances->removeElement($echeance);
    }

    /**
     * Get echeances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEcheances()
    {
        return $this->echeances;
    }

    /**
     * Set montantCapital
     *
     * @param string $montantCapital
     *
     * @return Credit
     */
    public function setMontantCapital($montantCapital)
    {
        $this->montantCapital = $montantCapital;

        return $this;
    }

    /**
     * Set totalInteret
     *
     * @param float $totalInteret
     *
     * @return Credit
     */
    public function setTotalInteret($totalInteret)
    {
        $this->totalInteret = $totalInteret;

        return $this;
    }

    /**
     * Get totalInteret
     *
     * @return float
     */
    public function getTotalInteret()
    {
        return $this->totalInteret;
    }

    /**
     * Get montantCapital
     *
     * @return float
     */
    public function getMontantCapital()
    {
        return $this->montantCapital;
    }

    /**
     * Set totalHorsAssurance
     *
     * @param float $totalHorsAssurance
     *
     * @return Credit
     */
    public function setTotalHorsAssurance($totalHorsAssurance)
    {
        $this->totalHorsAssurance = $totalHorsAssurance;

        return $this;
    }

    /**
     * Get totalHorsAssurance
     *
     * @return float
     */
    public function getTotalHorsAssurance()
    {
        return $this->totalHorsAssurance;
    }

    /**
     * Set totalAssurance
     *
     * @param float $totalAssurance
     *
     * @return Credit
     */
    public function setTotalAssurance($totalAssurance)
    {
        $this->totalAssurance = $totalAssurance;

        return $this;
    }

    /**
     * Get totalAssurance
     *
     * @return float
     */
    public function getTotalAssurance()
    {
        return $this->totalAssurance;
    }

    /**
     * Set totalPaye
     *
     * @param float $totalPaye
     *
     * @return Credit
     */
    public function setTotalPaye($totalPaye)
    {
        $this->totalPaye = $totalPaye;

        return $this;
    }

    /**
     * Get totalPaye
     *
     * @return float
     */
    public function getTotalPaye()
    {
        return $this->totalPaye;
    }

    /**
     * Set duree
     *
     * @param integer $duree
     *
     * @return Credit
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return integer
     */
    public function getDuree()
    {
        if ($this->duree) {
            return $this->duree . ' mois';
        }
        
        return null;
    }

    /**
     * Set periodicite
     *
     * @param string $periodicite
     *
     * @return Credit
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

    public function getDureeMois()
    {
        return $this->duree . ' mois';
    }
    
    /**
     * Set codeTaux
     *
     * @param float $codeTaux
     *
     * @return Credit
     */
    public function setCodeTaux($codeTaux)
    {
        $this->codeTaux = $codeTaux;

        return $this;
    }

    /**
     * Get codeTaux
     *
     * @return float
     */
    public function getCodeTaux()
    {
        return $this->codeTaux;
    }
    
    /**
     * Set typeTaux
     *
     * @param float $typeTaux
     *
     * @return Credit
     */
    public function setTypeTaux($typeTaux)
    {
        $this->typeTaux = $typeTaux;

        return $this;
    }

    /**
     * Get typeTaux
     *
     * @return float
     */
    public function getTypeTaux()
    {
        return $this->typeTaux;
    }

    /**
     * Set taux
     *
     * @param float $taux
     *
     * @return Credit
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get taux
     *
     * @return float
     */
    public function getTaux()
    {
        if (!trim($this->taux)) {
            return null;
        }
        
        if (trim($this->typeTaux) == 'F' || trim($this->typeTaux) == '') {
            return number_format($this->taux, 2, ',', '') . '%';
        } elseif (trim($this->typeTaux) == 'V' || trim($this->typeTaux) != '') {
            return $this->codeTaux . ' + ' . number_format($this->margeTaux, 2, ',', '') . '%';
        }
    }

    /**
     * Set margeTaux
     *
     * @param float $margeTaux
     *
     * @return Credit
     */
    public function setMargeTaux($margeTaux)
    {
        $this->margeTaux = $margeTaux;

        return $this;
    }

    /**
     * Get margeTaux
     *
     * @return float
     */
    public function getMargeTaux()
    {
        return $this->margeTaux;
    }
    
    /**
     * Set titre
     *
     * @param float $titre
     *
     * @return Credit
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return float
     */
    public function getTitre()
    {
        return $this->titre;
    }
    
    public function getTitre2()
    {
        if (trim($this->typeTaux) == 'F' || trim($this->typeTaux) == '') {
            return 'TAUX FIXE';
        } elseif (trim($this->typeTaux) == 'V' || trim($this->typeTaux) != '') {
            return 'TAUX VARIABLE';
        }
        
        return null;
    }
    
    public function getLibelleTauxNominal()
    {
        if (trim($this->typeTaux) == 'F' || trim($this->typeTaux) == '') {
            return 'Taux nominal annuel :';
        } elseif (trim($this->typeTaux) == 'V' || trim($this->typeTaux) != '') {
            return 'Taux nominal annuel indicatif :';
        }
        
        return null;
    }
    
    public function getLibelleTEG()
    {
        if (trim($this->typeTaux) == 'F' || trim($this->typeTaux) == '') {
            return 'TEG :';
        } elseif (trim($this->typeTaux) == 'V' || trim($this->typeTaux) != '') {
            return 'TEG indicatif :';
        }
        
        return null;
    }
    
    /**
     * Set dateEdition
     *
     * @param float $dateEdition
     *
     * @return Credit
     */
    public function setDateEdition($dateEdition)
    {
        $this->dateEdition = $dateEdition;

        return $this;
    }

    /**
     * Get dateEdition
     *
     * @return float
     */
    public function getDateEdition()
    {
        return $this->dateEdition;
    }
}
