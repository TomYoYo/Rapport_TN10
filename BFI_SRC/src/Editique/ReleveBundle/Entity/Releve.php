<?php

namespace Editique\ReleveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Releve
 *
 * ORM\Table()
 * @ORM\Entity()
 */
class Releve
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
     * @ORM\Column(name="fileSource", type="string", length=255)
     */
    private $fileSource;

    /**
     * @var string
     *
     * @ORM\Column(name="fileTarget", type="string", length=255)
     */
    private $fileTarget;

    /**
     * @var string
     *
     * @ORM\Column(name="dateDebut", type="string", length=10)
     */
    private $dateDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFin", type="string", length=10)
     */
    private $dateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="numReleve", type="integer")
     */
    private $numReleve;

    /**
     * @var integer
     *
     * @ORM\Column(name="ideSab", type="integer")
     */
    private $ideSab;

    /**
     * @var string
     *
     * @ORM\Column(name="idClient", type="string")
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="numCompte", type="string", length=11)
     */
    private $numCompte = ' ';

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=30)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="titulaire1", type="string", length=255)
     */
    private $titulaire1;
    
    /**
     * @var string
     *
     * @ORM\Column(name="titulaire2", type="string", length=255)
     */
    private $titulaire2;

    /**
     * @var string
     *
     * @ORM\Column(name="adFinale", type="string", length=255)
     */
    private $adFinale;

    /**
     * @var array
     *
     * @ORM\Column(name="messageCommercial", type="string", length=255)
     */
    private $messageCommerciaux;

    /**
     * @var string
     *
     * @ORM\Column(name="txRemuneration", type="string", length=255)
     */
    private $txRemuneration;

    /**
     * @var string
     *
     * @ORM\Column(name="majRemuneration", type="string", length=255)
     */
    private $majRemuneration;

    /**
     * @var string
     *
     * @ORM\Column(name="totalInteretAcquis", type="string", length=255)
     */
    private $totalInteretAcquis;

    /**
     * @var string
     *
     * @ORM\Column(name="totalInteretDebiteur", type="string", length=255)
     */
    private $totalInteretDebiteur;

    /**
     * @var string
     *
     * @ORM\Column(name="txInteret", type="string", length=255)
     */
    private $txInteret;

    /**
     * @var string
     *
     * @ORM\Column(name="commissionsDebiteur", type="string", length=255)
     */
    private $commissionsDebiteur;

    /**
     * @var string
     *
     * @ORM\Column(name="totalCommissions", type="string", length=255)
     */
    private $totalCommissions;

    /**
     * @var float
     *
     * @ORM\Column(name="TEG", type="float")
     */
    private $tEG;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFinPrecedente", type="string", length=10)
     */
    private $dateFinPrecedente;

    /**
     * @var float
     *
     * @ORM\Column(name="ancienSoldeDebiteur", type="float")
     */
    private $ancienSoldeDebiteur;

    /**
     * @var float
     *
     * @ORM\Column(name="ancienSoldeCrediteur", type="float")
     */
    private $ancienSoldeCrediteur;

    /**
     * @var float
     *
     * @ORM\Column(name="totalMvtDebiteur", type="float")
     */
    private $totalMvtDebiteur;

    /**
     * @var float
     *
     * @ORM\Column(name="totalMvtCrediteur", type="float")
     */
    private $totalMvtCrediteur;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeDebiteur", type="float")
     */
    private $soldeDebiteur;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeCrediteur", type="float")
     */
    private $soldeCrediteur;

    /**
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="releve")
     */
    private $operations;

    /**
     * @var string
     *
     * @ORM\Column(name="domiciliation", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="typeCompte", type="string", length=255)
     */
    private $typeCompte;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleCompte", type="string", length=255)
     */
    private $libelleCompte;

    /**
     * @var string
     *
     * @ORM\Column(name="modeDiffusion", type="string", length=255)
     */
    private $modeDiffusion;

    // pour les pdf
    public $enchainementTpl = array();
    public $startAndLength = array();
    public $nbPage = 0;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fileSource
     *
     * @param string $fileSource
     * @return Releve
     */
    public function setFileSource($fileSource)
    {
        $this->fileSource = $fileSource;

        return $this;
    }

    /**
     * Get fileSource
     *
     * @return string
     */
    public function getFileSource()
    {
        return $this->fileSource;
    }

    /**
     * Set fileTarget
     *
     * @param string $fileTarget
     * @return Releve
     */
    public function setFileTarget($fileTarget)
    {
        $this->fileTarget = $fileTarget;

        return $this;
    }

    /**
     * Get fileTarget
     *
     * @return string
     */
    public function getFileTarget()
    {
        return $this->fileTarget;
    }

    /**
     * Set dateDebut
     *
     * @param string $dateDebut
     * @return Releve
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return string
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param string $dateFin
     * @return Releve
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return string
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set numReleve
     *
     * @param integer $numReleve
     * @return Releve
     */
    public function setNumReleve($numReleve)
    {
        $this->numReleve = $numReleve;

        return $this;
    }

    /**
     * Get numReleve
     *
     * @return integer
     */
    public function getNumReleve()
    {
        return $this->numReleve ? $this->numReleve : null;
    }

    /**
     * Set ideSab
     *
     * @param integer $ideSab
     * @return Releve
     */
    public function setIdeSab($ideSab)
    {
        $this->ideSab = $ideSab;

        return $this;
    }

    /**
     * Get ideSab
     *
     * @return integer
     */
    public function getIdeSab()
    {
        return $this->ideSab;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     * @return Releve
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
     * Set numCompte
     *
     * @param string $numCompte
     * @return Releve
     */
    public function setNumCompte($numCompte)
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    /**
     * Get numCompte
     *
     * @return string
     */
    public function getNumCompte()
    {
        return $this->numCompte;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     * @return Releve
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set titulaire1
     *
     * @param string $titulaire1
     * @return Releve
     */
    public function setTitulaire1($titulaire1)
    {
        $this->titulaire1 = $titulaire1;

        return $this;
    }

    /**
     * Get titulaire1
     *
     * @return string
     */
    public function getTitulaire1()
    {
        return $this->titulaire1;
    }
    
    /**
     * Set titulaire2
     *
     * @param string $titulaire2
     * @return Releve
     */
    public function setTitulaire2($titulaire2)
    {
        $this->titulaire2 = $titulaire2;

        return $this;
    }

    /**
     * Get titulaire2
     *
     * @return string
     */
    public function getTitulaire2()
    {
        return $this->titulaire2;
    }

    /**
     * Set adFinale
     *
     * @param string $adFinale
     * @return Releve
     */
    public function setAdFinale($adFinale)
    {
        $this->adFinale = $adFinale;

        return $this;
    }

    /**
     * Get adFinale
     *
     * @return string
     */
    public function getAdFinale()
    {
        return $this->adFinale;
    }

    /**
     * Set messageCommercial
     *
     * @param string $messageCommercial
     * @return Releve
     */
    public function setMessageCommercial($messageCommercial)
    {
        $this->messageCommercial = $messageCommercial;

        return $this;
    }

    /**
     * Get messageCommercial
     *
     * @return string
     */
    public function getMessageCommercial()
    {
        return $this->messageCommercial;
    }

    /**
     * Set txRemuneration
     *
     * @param string $txRemuneration
     * @return Releve
     */
    public function setTxRemuneration($txRemuneration)
    {
        $this->txRemuneration = $txRemuneration;

        return $this;
    }

    /**
     * Get txRemuneration
     *
     * @return string
     */
    public function getTxRemuneration()
    {
        if (!is_null($this->txRemuneration)) {
            return number_format(str_replace(array('.', ','), array('', '.'), $this->txRemuneration), 2, ',', ' ').' %';
        }
        
        return null;
    }

    /**
     * Get txRemuneration
     *
     * @return string
     */
    public function getTxRemunerationGlobal()
    {
        if (!is_null($this->txRemuneration)) {
            return number_format(
                str_replace(array('.', ','), array('', '.'), $this->txRemuneration),
                2,
                ',',
                ' '
            ) . ' % au ' . $this->majRemuneration;
        }
        
        return null;
    }

    /**
     * Set tEG
     *
     * @param float $tEG
     * @return Releve
     */
    public function setTEG($tEG)
    {
        $this->tEG = $tEG;

        return $this;
    }

    /**
     * Get tEG
     *
     * @return float
     */
    public function getTEG()
    {
        if (!is_null($this->tEG)) {
            return number_format($this->tEG, 2, ',', ' ') . " %";
        }
        
        return null;
    }

    /**
     * Set dateFinPrecedente
     *
     * @param string $dateFinPrecedente
     * @return Releve
     */
    public function setDateFinPrecedente($dateFinPrecedente)
    {
        $this->dateFinPrecedente = $dateFinPrecedente;

        return $this;
    }

    /**
     * Get dateFinPrecedente
     *
     * @return string
     */
    public function getDateFinPrecedente()
    {
        return $this->dateFinPrecedente;
    }

    /**
     * Set ancienSoldeDebiteur
     *
     * @param float $ancienSoldeDebiteur
     * @return Releve
     */
    public function setAncienSoldeDebiteur($ancienSoldeDebiteur)
    {
        $this->ancienSoldeDebiteur = $ancienSoldeDebiteur;

        return $this;
    }

    /**
     * Get ancienSoldeDebiteur
     *
     * @return float
     */
    public function getAncienSoldeDebiteur()
    {
        return number_format($this->ancienSoldeDebiteur, 2, ',', ' ');
    }

    /**
     * Set ancienSoldeCrediteur
     *
     * @param float $ancienSoldeCrediteur
     * @return Releve
     */
    public function setAncienSoldeCrediteur($ancienSoldeCrediteur)
    {
        $this->ancienSoldeCrediteur = $ancienSoldeCrediteur;

        return $this;
    }

    /**
     * Get ancienSoldeCrediteur
     *
     * @return float
     */
    public function getAncienSoldeCrediteur()
    {
        return number_format($this->ancienSoldeCrediteur, 2, ',', ' ');
    }

    /**
     * Set totalMvtDebiteur
     *
     * @param float $totalMvtDebiteur
     * @return Releve
     */
    public function setTotalMvtDebiteur($totalMvtDebiteur)
    {
        $this->totalMvtDebiteur = $totalMvtDebiteur;

        return $this;
    }

    /**
     * Get totalMvtDebiteur
     *
     * @return float
     */
    public function getTotalMvtDebiteur()
    {
        return number_format($this->totalMvtDebiteur, 2, ',', ' ');
    }

    /**
     * Set totalMvtCrediteur
     *
     * @param float $totalMvtCrediteur
     * @return Releve
     */
    public function setTotalMvtCrediteur($totalMvtCrediteur)
    {
        $this->totalMvtCrediteur = $totalMvtCrediteur;

        return $this;
    }

    /**
     * Get totalMvtCrediteur
     *
     * @return float
     */
    public function getTotalMvtCrediteur()
    {
        return number_format($this->totalMvtCrediteur, 2, ',', ' ');
    }

    /**
     * Set soldeDebiteur
     *
     * @param float $soldeDebiteur
     * @return Releve
     */
    public function setSoldeDebiteur($soldeDebiteur)
    {
        $this->soldeDebiteur = $soldeDebiteur;

        return $this;
    }

    /**
     * Get soldeDebiteur
     *
     * @return float
     */
    public function getSoldeDebiteur()
    {
        return number_format($this->soldeDebiteur, 2, ',', ' ');
    }

    /**
     * Set soldeCrediteur
     *
     * @param float $soldeCrediteur
     * @return Releve
     */
    public function setSoldeCrediteur($soldeCrediteur)
    {
        $this->soldeCrediteur = $soldeCrediteur;

        return $this;
    }

    /**
     * Get soldeCrediteur
     *
     * @return float
     */
    public function getSoldeCrediteur()
    {
        return number_format($this->soldeCrediteur, 2, ',', ' ');
    }

    /**
     * Add operation
     *
     * @param \Editique\ReleveBundle\Entity\Operation $operation
     * @return Releve
     */
    public function addOperation(\Editique\ReleveBundle\Entity\Operation $operation)
    {
        $this->operations[] = $operation;
        return $this;
    }

    /**
     * Remove operation
     *
     * @param \Editique\ReleveBundle\Entity\Operation $operation
     */
    public function removeOperation(\Editique\ReleveBundle\Entity\Operation $operation)
    {
        $this->operations->removeElement($operation);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Set Adresse
     *
     * @param string $adresse
     * @return Releve
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get Adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set messageCommerciaux
     *
     * @param string $messageCommerciaux
     * @return Releve
     */
    public function setMessageCommerciaux($messageCommerciaux)
    {
        $this->messageCommerciaux = $messageCommerciaux;

        return $this;
    }

    /**
     * Get messageCommerciaux
     *
     * @return string
     */
    public function getMessageCommerciaux()
    {
        return $this->messageCommerciaux;
    }

    /**
     * Set majRemuneration
     *
     * @param string $majRemuneration
     * @return Releve
     */
    public function setMajRemuneration($majRemuneration)
    {
        $this->majRemuneration = $majRemuneration;

        return $this;
    }

    /**
     * Get majRemuneration
     *
     * @return string
     */
    public function getMajRemuneration()
    {
        return $this->majRemuneration;
    }

    /**
     * Set totalInteretAcquis
     *
     * @param string $totalInteretAcquis
     * @return Releve
     */
    public function setTotalInteretAcquis($totalInteretAcquis)
    {
        $this->totalInteretAcquis = $totalInteretAcquis;

        return $this;
    }

    /**
     * Get totalInteretAcquis
     *
     * @return string
     */
    public function getTotalInteretAcquis()
    {
        return $this->totalInteretAcquis ? number_format($this->totalInteretAcquis, 2, ',', ' ') : null;
    }
    
    /**
     * Get totalInteretAcquis
     *
     * @return string
     */
    public function getTotalInteretAcquisGlobal()
    {
        if ($this->totalInteretAcquis) {
            return number_format($this->totalInteretAcquis, 2, ',', ' ') . ' euros au ' . $this->dateFin;
        }
        
        return null;
    }

    /**
     * Set totalInteretDebiteur
     *
     * @param string $totalInteretDebiteur
     * @return Releve
     */
    public function setTotalInteretDebiteur($totalInteretDebiteur)
    {
        $this->totalInteretDebiteur = $totalInteretDebiteur;

        return $this;
    }

    /**
     * Get totalInteretDebiteur
     *
     * @return string
     */
    public function getTotalInteretDebiteur()
    {
        return $this->totalInteretDebiteur ? number_format($this->totalInteretDebiteur, 2, ',', ' ') . ' euros' : null;
    }

    /**
     * Set txInteret
     *
     * @param string $txInteret
     * @return Releve
     */
    public function setTxInteret($txInteret)
    {
        $this->txInteret = $txInteret;

        return $this;
    }

    /**
     * Get txInteret
     *
     * @return string
     */
    public function getTxInteret()
    {
        if (!is_null($this->txInteret)) {
            return number_format(str_replace(array('.', ','), array('', '.'), $this->txInteret), 2, ',', ' ') . ' %';
        }
        
        return null;
    }

    /**
     * Set commissionsDebiteur
     *
     * @param string $commissionsDebiteur
     * @return Releve
     */
    public function setCommissionsDebiteur($commissionsDebiteur)
    {
        $this->commissionsDebiteur = $commissionsDebiteur;

        return $this;
    }

    /**
     * Get commissionsDebiteur
     *
     * @return string
     */
    public function getCommissionsDebiteur()
    {
        return $this->commissionsDebiteur ? number_format($this->commissionsDebiteur, 2, ',', ' ') . " euros" : null;
    }

    /**
     * Set totalCommissions
     *
     * @param string $totalCommissions
     * @return Releve
     */
    public function setTotalCommissions($totalCommissions)
    {
        $this->totalCommissions = $totalCommissions;

        return $this;
    }

    /**
     * Get totalCommissions
     *
     * @return string
     */
    public function getTotalCommissions()
    {
        return $this->totalCommissions ? number_format($this->totalCommissions, 2, ',', ' ') . " euros" : null;
    }

    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;
    }

    public function getTypeCompte()
    {
        return $this->typeCompte;
    }
    
    public function setLibelleCompte($libelleCompte)
    {
        $this->libelleCompte = $libelleCompte;
    }

    public function getLibelleCompte()
    {
        return $this->libelleCompte;
    }

    public function setModeDiffusion($modeDiffusion)
    {
        $this->modeDiffusion = $modeDiffusion;
    }

    public function getModeDiffusion()
    {
        return $this->modeDiffusion;
    }

    public function getLibelleCompteGlobal()
    {
        return 'RELEVE DE '. $this->libelleCompte;
    }

    public function isCompteCourantPro()
    {
        return $this->typeCompte === 'COPRO';
    }

    public function isCompteCourantPart()
    {
        return $this->typeCompte === 'COPAR';
    }
}
