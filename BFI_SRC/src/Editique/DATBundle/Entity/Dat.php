<?php

namespace Editique\DATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dat
 *
 * @ORM\Table(name="ZDATBLO0")
 * @ORM\Entity()
 *
 */
class Dat
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
     * @ORM\Column(name="DATBLONUC", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOCL1", type="string", length=32)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOCL2", type="string", length=32)
     */
    private $prenom;

     /**
     * @var string
     *
     * @ORM\Column(name="DATBLOAD1", type="string", length=38)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOAD2", type="string", length=38)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOAD3", type="string", length=38)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOAD4", type="string", length=38)
     */
    private $adresse4;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOCPO", type="string", length=6)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOVIL", type="string", length=32)
     */
    private $ville;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATBLONUM", type="integer")
     */
    private $numOpe;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATBLODOS", type="integer")
     */
    private $numDos;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATBLODIS", type="integer")
     */
    private $dateDebut;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATBLOFPR", type="integer")
     */
    private $dateEcheance;

    /**
     * @var float
     *
     * @ORM\Column(name="DATBLOCAP", type="float")
     */
    private $montantDepot;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLOCPM", type="string", length=20)
     */
    private $numCompteSupport;

    /**
     * @var string
     *
     * @ORM\Column(name="DATBLONAT", type="string", length=3)
     */
    private $typeRemuneration;

    /**
     * @var float
     *
     * @ORM\Column(name="DATBLOACT", type="float")
     */
    private $tauxActuriel;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATBLODTE", type="integer")
     */
    private $dateEdition;

    /**
     *
     * ORM\OneToMany(targetEntity="PeriodeTaux", mappedBy="dat", fetch="EAGER")
     * on ne met pas de lien car cela ne marche pas, souci bizarre peut etre du
     * au fait qu'on ne peut pas laisser doctrine faire un schema update...
     */
    private $periodeTaux;

    /**
     * @var float
     *
     * @ORM\Column(name="DATBLOVTX", type="float")
     */
    private $tauxNominal;

    // attributs a aller chercher sur d'autres tables
    // SIREN (ou RCS si absent)
    private $siren = '';
    private $nomRepresentant = '';
    private $prenomRepresentant = '';



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
     * @return Dat
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
     * Set nom
     *
     * @param string $nom
     * @return Dat
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Dat
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

     /**
     * Set adresse1
     *
     * @param string $adresse1
     * @return Dat
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
     * @return Dat
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
     * @return Dat
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
     * @return Dat
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
     * @return Dat
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
     * @return Dat
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
     * Set numOpe
     *
     * @param integer $numOpe
     * @return Dat
     */
    public function setNumOpe($numOpe)
    {
        $this->numOpe = $numOpe;

        return $this;
    }

    /**
     * Get numOpe
     *
     * @return integer
     */
    public function getNumOpe()
    {
        return $this->numOpe;
    }

    /**
     * Set numDos
     *
     * @param integer $numDos
     * @return Dat
     */
    public function setNumDos($numDos)
    {
        $this->numDos = $numDos;

        return $this;
    }

    /**
     * Get numDos
     *
     * @return integer
     */
    public function getNumDos()
    {
        return $this->numDos;
    }

    /**
     * Set dateDebut
     *
     * @param integer $dateDebut
     * @return Dat
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return integer
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function getDateDebutFromSab()
    {
        // SAB : 1AAMMJJ
        $annee = '20' . substr($this->dateDebut, 1, 2);
        $mois = substr($this->dateDebut, 3, 2);
        $jour = substr($this->dateDebut, 5, 2);

        // nous JJ/MM/YYYY
        return $jour . '/' . $mois . '/' . $annee;
    }

    /**
     * Set dateEcheance
     *
     * @param integer $dateEcheance
     * @return Dat
     */
    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return integer
     */
    public function getDateEcheance()
    {
        return $this->dateEcheance;
    }

    public function getDateEcheanceFromSab()
    {
        // SAB : 1AAMMJJ
        $annee = '20' . substr($this->dateEcheance, 1, 2);
        $mois = substr($this->dateEcheance, 3, 2);
        $jour = substr($this->dateEcheance, 5, 2);

        // nous JJ/MM/YYYY
        return $jour . '/' . $mois . '/' . $annee;
    }

    /**
     * Set montantDepot
     *
     * @param float $montantDepot
     * @return Dat
     */
    public function setMontantDepot($montantDepot)
    {
        $this->montantDepot = $montantDepot;

        return $this;
    }

    /**
     * Get montantDepot
     *
     * @return float
     */
    public function getMontantDepot()
    {
        return $this->montantDepot;
    }

    /**
     * Set numCompteSupport
     *
     * @param string $numCompteSupport
     * @return Dat
     */
    public function setNumCompteSupport($numCompteSupport)
    {
        $this->numCompteSupport = $numCompteSupport;

        return $this;
    }

    /**
     * Get numCompteSupport
     *
     * @return string
     */
    public function getNumCompteSupport()
    {
        return $this->numCompteSupport;
    }

    /**
     * Set typeRemuneration
     *
     * @param string $typeRemuneration
     * @return Dat
     */
    public function setTypeRemuneration($typeRemuneration)
    {
        $this->typeRemuneration = $typeRemuneration;

        return $this;
    }

    /**
     * Get typeRemuneration
     *
     * @return string
     */
    public function getTypeRemuneration()
    {
        return $this->typeRemuneration;
    }

    /**
     * Set tauxActuriel
     *
     * @param float $tauxActuriel
     * @return Dat
     */
    public function setTauxActuriel($tauxActuriel)
    {
        $this->tauxActuriel = $tauxActuriel;

        return $this;
    }

    /**
     * Get tauxActuriel
     *
     * @return float
     */
    public function getTauxActuriel()
    {
        return number_format($this->tauxActuriel, 2, ',', ' ').'%';
    }

    /**
     * Set dateEdition
     *
     * @param integer $dateEdition
     * @return Dat
     */
    public function setDateEdition($dateEdition)
    {
        $this->dateEdition = $dateEdition;

        return $this;
    }

    /**
     * Get dateEdition
     *
     * @return integer
     */
    public function getDateEdition()
    {
        if ($this->dateEdition == 0) {
            return date('1ymd');
        }
        return $this->dateEdition;

    }

    public function getDateEditionFromSab()
    {
        if ($this->dateEdition == 0) {
            return date('d/m/Y');
        }
        // SAB : 1AAMMJJ
        $annee = '20' . substr($this->dateEdition, 1, 2);
        $mois = substr($this->dateEdition, 3, 2);
        $jour = substr($this->dateEdition, 5, 2);

        // nous JJ/MM/YYYY
        return $jour . '/' . $mois . '/' . $annee;
    }

    public function getAdresseFinale()
    {
        $adresseFinale = array();
        if (trim($this->adresse1) != '') {
            $adresseFinale[]= trim($this->adresse1);
        }
        if (trim($this->adresse2) != '') {
            $adresseFinale[]= trim($this->adresse2);
        }
        if (trim($this->adresse3) != '') {
            $adresseFinale[]= trim($this->adresse3);
        }
        if (trim($this->adresse4) != '') {
            $adresseFinale[]= trim($this->adresse4);
        }
        $adresseFinale[]= trim($this->codePostal).' '.trim($this->ville);
        switch(sizeof($adresseFinale)) {
            case 0:
                $adresseFinale=array(' ',' ',' ',' ',' ');
                break;
            case 1:
                array_unshift($adresseFinale, ' ');
                array_unshift($adresseFinale, ' ');
                array_push($adresseFinale, ' ');
                array_push($adresseFinale, ' ');
                break;
            case 2:
                array_unshift($adresseFinale, ' ');
                array_push($adresseFinale, ' ');
                array_push($adresseFinale, ' ');
                break;
            case 3:
                array_unshift($adresseFinale, ' ');
                array_push($adresseFinale, ' ');
                break;
            case 4:
                array_push($adresseFinale, ' ');
                break;
        }
        return $adresseFinale;

    }

    public function getVilleFinale()
    {
        return trim($this->codePostal) . ' ' . trim($this->ville);
    }

    public function getRefDat()
    {
        return
            trim($this->numOpe) .
            ' - ' .
            trim($this->numDos)
        ;
    }

    public function getMontantDepotFinal()
    {
        $montant = trim(number_format($this->montantDepot, 2, ',', ' ')) .
            ' euros (' .
            \BackOffice\ParserBundle\Manager\EcritureManager::asLetters($this->montantDepot) .
            ' euros)'
        ;
        return array(
            substr($montant, 0, 69),
            substr($montant, 69, 69)
        );
    }

    public function isDATFixe()
    {
        return $this->typeRemuneration === 'DFX';
    }

    public function isDATProgressif()
    {
        return $this->typeRemuneration === 'DTP';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periodeTaux = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add periodeTaux
     *
     * @param \Editique\DATBundle\Entity\PeriodeTaux $periodeTaux
     * @return Dat
     */
    public function addPeriodeTaux(\Editique\DATBundle\Entity\PeriodeTaux $periodeTaux)
    {
        $this->periodeTaux[] = $periodeTaux;

        return $this;
    }

    /**
     * Remove periodeTaux
     *
     * @param \Editique\DATBundle\Entity\PeriodeTaux $periodeTaux
     */
    public function removePeriodeTaux(\Editique\DATBundle\Entity\PeriodeTaux $periodeTaux)
    {
        $this->periodeTaux->removeElement($periodeTaux);
    }

    /**
     * Get periodeTaux
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodeTaux()
    {
        return $this->periodeTaux;
    }

    /**
     * Set periodeTaux
     *
     * @return Dat
     */
    public function setPeriodeTaux($tab)
    {
        $this->periodeTaux = $tab;
        return $this;
    }

    /**
     * Set tauxNominal
     *
     * @param float $tauxNominal
     *
     * @return Dat
     */
    public function setTauxNominal($tauxNominal)
    {
        $this->tauxNominal = $tauxNominal;

        return $this;
    }

    /**
     * Get tauxNominal
     *
     * @return float
     */
    public function getTauxNominal()
    {
        return number_format($this->tauxNominal, 2, ',', ' ').'%';
    }

    /**
     * Set siren
     *
     * @param string $siren
     * @return Dat
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set nomRepresentant
     *
     * @param string $nom
     * @return Dat
     */
    public function setNomRepresentant($nom)
    {
        $this->nomRepresentant = $nom;

        return $this;
    }

    /**
     * Get nomRepresentant
     *
     * @return string
     */
    public function getNomRepresentant()
    {
        return $this->nomRepresentant;
    }

    /**
     * Set prenomRepresentant
     *
     * @param string $nom
     * @return Dat
     */
    public function setPrenomRepresentant($prenom)
    {
        $this->prenomRepresentant = $prenom;

        return $this;
    }

    /**
     * Get prenomRepresentant
     *
     * @return string
     */
    public function getPrenomRepresentant()
    {
        return $this->prenomRepresentant;
    }
}
