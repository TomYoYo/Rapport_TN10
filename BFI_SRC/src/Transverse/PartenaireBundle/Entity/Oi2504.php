<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transverse\PartenaireBundle\Entity\Oi250109;

/**
 * Role
 *
 * @ORM\Table("TRANSVERSE_OI25_04")
 * @ORM\Entity
 */
class Oi2504
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
     * @ORM\Column(name="numDossierGESICA", type="string", length=12, nullable=true)
     */
    private $numDossierGESICA;

    /**
     * @var string
     *
     * @ORM\Column(name="typeDossier", type="string", length=12, nullable=true)
     */
    private $typeDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="reseau", type="string", length=2, nullable=true)
     */
    private $reseau;

    /**
     * @var string
     *
     * @ORM\Column(name="typeEvenement", type="string", length=6, nullable=true)
     */
    private $typeEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="numCycle", type="string", length=1, nullable=true)
     */
    private $numCycle;

    /**
     * @var string
     *
     * @ORM\Column(name="caracteristiqueEvenement", type="string", length=1, nullable=true)
     */
    private $caracteristiqueEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="typeTransaction", type="string", length=3, nullable=true)
     */
    private $typeTransaction;


    /**
     * @var string
     *
     * @ORM\Column(name="sensEvenement", type="string", length=1, nullable=true)
     */
    private $sensEvenement;


    /**
     * @var string
     *
     * @ORM\Column(name="motifEvenement", type="string", length=4, nullable=true)
     */
    private $motifEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleMotifEvenement", type="string", length=50, nullable=true)
     */
    private $libelleMotifEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroIsoCarte", type="string", length=19, nullable=true)
     */
    private $numeroIsoCarte;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPrenomPorteur", type="string", length=60, nullable=true)
     */
    private $nomPrenomPorteur;


    /**
     * @var string
     *
     * @ORM\Column(name="motifOppositionCarte", type="string", length=1, nullable=true)
     */
    private $motifOppositionCarte;


    /**
     * @var string
     *
     * @ORM\Column(name="dateOppositionCarte", type="string", length=8, nullable=true)
     */
    private $dateOppositionCarte;


    /**
     * @var string
     *
     * @ORM\Column(name="formatCompteImpute", type="string", length=3, nullable=true)
     */
    private $formatCompteImpute;


    /**
     * @var string
     *
     * @ORM\Column(name="compteImpute", type="string", length=40, nullable=true)
     */
    private $compteImpute;



    /**
     * @var string
     *
     * @ORM\Column(name="libelleComptable", type="string", length=100, nullable=true)
     */
    private $libelleComptable;


    /**
     * @var string
     *
     * @ORM\Column(name="montantImpute", type="string", length=16, nullable=true)
     */
    private $montantImpute;



    /**
     * @var string
     *
     * @ORM\Column(name="deviseMontantImpute", type="string", length=3, nullable=true)
     */
    private $deviseMontantImpute;



    /**
     * @var string
     *
     * @ORM\Column(name="nombreDecimalesDeviseImpute", type="string", length=1, nullable=true)
     */
    private $nombreDecimalesDeviseImpute;




    /**
     * @var string
     *
     * @ORM\Column(name="typeMontantImpute", type="string", length=1, nullable=true)
     */
    private $typeMontantImpute;



    /**
     * @var string
     *
     * @ORM\Column(name="datePrelevementVirement", type="string", length=8, nullable=true)
     */
    private $datePrelevementVirement;


    /**
     * @var string
     *
     * @ORM\Column(name="sensOperation", type="string", length=1, nullable=true)
     */
    private $sensOperation;

    /**
     * @var string
     *
     * @ORM\Column(name="dateAchat", type="string", length=8, nullable=true)
     */
    private $dateAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="dateCompensation", type="string", length=8, nullable=true)
     */
    private $dateCompensation;

    /**
     * @var string
     *
     * @ORM\Column(name="montantAchatBrut", type="string", length=16, nullable=true)
     */
    private $montantAchatBrut;

    /**
     * @var string
     *
     * @ORM\Column(name="deviseMontantAchat", type="string", length=3, nullable=true)
     */
    private $deviseMontantAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreDecimalesDeviseAchat", type="string", length=1, nullable=true)
     */
    private $nombreDecimalesDeviseAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="montantAchatDeviseOrigine", type="string", length=16, nullable=true)
     */
    private $montantAchatDeviseOrigine;

    /**
     * @var string
     *
     * @ORM\Column(name="deviseOrigine", type="string", length=3, nullable=true)
     */
    private $deviseOrigine;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreDecimaleDeviseOrigine", type="string", length=1, nullable=true)
     */
    private $nombreDecimaleDeviseOrigine;


    /**
     * @var string
     *
     * @ORM\Column(name="montantCompenseEuro", type="string", length=16, nullable=true)
     */
    private $montantCompenseEuro;



    /**
     * @var string
     *
     * @ORM\Column(name="deviseCompensation", type="string", length=3, nullable=true)
     */
    private $deviseCompensation;


    /**
     * @var string
     *
     * @ORM\Column(name="nbDecimalesDeviseCompensation", type="string", length=1, nullable=true)
     */
    private $nbDecimalesDeviseCompensation;


    /**
     * @var string
     *
     * @ORM\Column(name="montantCommissionsBanqueInt", type="string", length=12, nullable=true)
     */
    private $montantCommissionsBanqueInt;


    /**
     * @var string
     *
     * @ORM\Column(name="deviseCommissionsBanque", type="string", length=3, nullable=true)
     */
    private $deviseCommissionsBanque;


    /**
     * @var string
     *
     * @ORM\Column(name="nbDecimalesDeviseCompensation2", type="string", length=1, nullable=true)
     */
    private $nbDecimalesDeviseCompensation2;


    /**
     * @var string
     *
     * @ORM\Column(name="montantCommissionsInterchange", type="string", length=12, nullable=true)
     */
    private $montantCommissionsInterchange;


    /**
     * @var string
     *
     * @ORM\Column(name="deviseCommissionsBanque2", type="string", length=3, nullable=true)
     */
    private $deviseCommissionsBanque2;


    /**
     * @var string
     *
     * @ORM\Column(name="nbDecimalesDeviseCompensation3", type="string", length=1, nullable=true)
     */
    private $nbDecimalesDeviseCompensation3;


    /**
     * @var string
     *
     * @ORM\Column(name="referenceUnique", type="string", length=16, nullable=true)
     */
    private $referenceUnique;


    /**
     * @var string
     *
     * @ORM\Column(name="ARN", type="string", length=23, nullable=true)
     */
    private $ARN;


    /**
     * @var string
     *
     * @ORM\Column(name="enseigneCommercant", type="string", length=40, nullable=true)
     */
    private $enseigneCommercant;


    /**
     * @var string
     *
     * @ORM\Column(name="siretCommercant", type="string", length=15, nullable=true)
     */
    private $siretCommercant;


    /**
     * @var string
     *
     * @ORM\Column(name="numeroContratCommercant", type="string", length=20, nullable=true)
     */
    private $numeroContratCommercant;


    /**
     * @var string
     *
     * @ORM\Column(name="referenceClient", type="string", length=20, nullable=true)
     */
    private $referenceClient;


    /**
     * @var string
     *
     * @ORM\Column(name="RIBTiers", type="string", length=3, nullable=true)
     */
    private $RIBTiers;


    /**
     * @var string
     *
     * @ORM\Column(name="RIBTiers2", type="string", length=40, nullable=true)
     */
    private $RIBTiers2;



    /**
     * @ORM\ManyToOne(targetEntity="Transverse\PartenaireBundle\Entity\Oi250109", inversedBy="oI2504s", cascade={"persist"})
     * @ORM\JoinColumn(name="OI25_0109_ID", referencedColumnName="id")
     */
    private $OI250109;

    public function setOi250109(Oi250109 $OI250109)
    {
        $this->OI250109 = $OI250109;
        return $this;
    }


    public function getOi250109()
    {
        return $this->OI250109;
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
     * Set numDossierGESICA
     *
     * @param string $numDossierGESICA
     *
     * @return OI25_04
     */
    public function setNumDossierGESICA($numDossierGESICA)
    {
        $this->numDossierGESICA = $numDossierGESICA;
    
        return $this;
    }

    /**
     * Get numDossierGESICA
     *
     * @return string 
     */
    public function getNumDossierGESICA()
    {
        return $this->numDossierGESICA;
    }

    /**
     * Set typeDossier
     *
     * @param string $typeDossier
     *
     * @return OI25_04
     */
    public function setTypeDossier($typeDossier)
    {
        $this->typeDossier = $typeDossier;
    
        return $this;
    }

    /**
     * Get typeDossier
     *
     * @return string 
     */
    public function getTypeDossier()
    {
        return $this->typeDossier;
    }

    /**
     * Set reseau
     *
     * @param string $reseau
     *
     * @return OI25_04
     */
    public function setReseau($reseau)
    {
        $this->reseau = $reseau;
    
        return $this;
    }

    /**
     * Get reseau
     *
     * @return string 
     */
    public function getReseau()
    {
        return $this->reseau;
    }

    /**
     * Set typeEvenement
     *
     * @param string $typeEvenement
     *
     * @return OI25_04
     */
    public function setTypeEvenement($typeEvenement)
    {
        $this->typeEvenement = $typeEvenement;
    
        return $this;
    }

    /**
     * Get typeEvenement
     *
     * @return string 
     */
    public function getTypeEvenement()
    {
        return $this->typeEvenement;
    }

    /**
     * Set numCycle
     *
     * @param string $numCycle
     *
     * @return OI25_04
     */
    public function setNumCycle($numCycle)
    {
        $this->numCycle = $numCycle;
    
        return $this;
    }

    /**
     * Get numCycle
     *
     * @return string 
     */
    public function getNumCycle()
    {
        return $this->numCycle;
    }

    /**
     * Set caracteristiqueEvenement
     *
     * @param string $caracteristiqueEvenement
     *
     * @return OI25_04
     */
    public function setCaracteristiqueEvenement($caracteristiqueEvenement)
    {
        $this->caracteristiqueEvenement = $caracteristiqueEvenement;
    
        return $this;
    }

    /**
     * Get caracteristiqueEvenement
     *
     * @return string 
     */
    public function getCaracteristiqueEvenement()
    {
        return $this->caracteristiqueEvenement;
    }

    /**
     * Set typeTransaction
     *
     * @param string $typeTransaction
     *
     * @return OI25_04
     */
    public function setTypeTransaction($typeTransaction)
    {
        $this->typeTransaction = $typeTransaction;
    
        return $this;
    }

    /**
     * Get typeTransaction
     *
     * @return string 
     */
    public function getTypeTransaction()
    {
        return $this->typeTransaction;
    }

    /**
     * Set sensEvenement
     *
     * @param string $sensEvenement
     *
     * @return OI25_04
     */
    public function setSensEvenement($sensEvenement)
    {
        $this->sensEvenement = $sensEvenement;
    
        return $this;
    }

    /**
     * Get sensEvenement
     *
     * @return string 
     */
    public function getSensEvenement()
    {
        return $this->sensEvenement;
    }

    /**
     * Set motifEvenement
     *
     * @param string $motifEvenement
     *
     * @return OI25_04
     */
    public function setMotifEvenement($motifEvenement)
    {
        $this->motifEvenement = $motifEvenement;
    
        return $this;
    }

    /**
     * Get motifEvenement
     *
     * @return string 
     */
    public function getMotifEvenement()
    {
        return $this->motifEvenement;
    }

    /**
     * Set libelleMotifEvenement
     *
     * @param string $libelleMotifEvenement
     *
     * @return OI25_04
     */
    public function setLibelleMotifEvenement($libelleMotifEvenement)
    {
        $this->libelleMotifEvenement = $libelleMotifEvenement;
    
        return $this;
    }

    /**
     * Get libelleMotifEvenement
     *
     * @return string 
     */
    public function getLibelleMotifEvenement()
    {
        return $this->libelleMotifEvenement;
    }

    /**
     * Set numeroIsoCarte
     *
     * @param string $numeroIsoCarte
     *
     * @return OI25_04
     */
    public function setNumeroIsoCarte($numeroIsoCarte)
    {
        $this->numeroIsoCarte = $numeroIsoCarte;
    
        return $this;
    }

    /**
     * Get numeroIsoCarte
     *
     * @return string 
     */
    public function getNumeroIsoCarte()
    {
        return $this->numeroIsoCarte;
    }

    /**
     * Set nomPrenomPorteur
     *
     * @param string $nomPrenomPorteur
     *
     * @return OI25_04
     */
    public function setNomPrenomPorteur($nomPrenomPorteur)
    {
        $this->nomPrenomPorteur = $nomPrenomPorteur;
    
        return $this;
    }

    /**
     * Get nomPrenomPorteur
     *
     * @return string 
     */
    public function getNomPrenomPorteur()
    {
        return $this->nomPrenomPorteur;
    }

    /**
     * Set motifOppositionCarte
     *
     * @param string $motifOppositionCarte
     *
     * @return OI25_04
     */
    public function setMotifOppositionCarte($motifOppositionCarte)
    {
        $this->motifOppositionCarte = $motifOppositionCarte;
    
        return $this;
    }

    /**
     * Get motifOppositionCarte
     *
     * @return string 
     */
    public function getMotifOppositionCarte()
    {
        return $this->motifOppositionCarte;
    }

    /**
     * Set dateOppositionCarte
     *
     * @param string $dateOppositionCarte
     *
     * @return OI25_04
     */
    public function setDateOppositionCarte($dateOppositionCarte)
    {
        $this->dateOppositionCarte = $dateOppositionCarte;
    
        return $this;
    }

    /**
     * Get dateOppositionCarte
     *
     * @return string 
     */
    public function getDateOppositionCarte()
    {
        return $this->dateOppositionCarte;
    }

    /**
     * Set formatCompteImpute
     *
     * @param string $formatCompteImpute
     *
     * @return OI25_04
     */
    public function setFormatCompteImpute($formatCompteImpute)
    {
        $this->formatCompteImpute = $formatCompteImpute;
    
        return $this;
    }

    /**
     * Get formatCompteImpute
     *
     * @return string 
     */
    public function getFormatCompteImpute()
    {
        return $this->formatCompteImpute;
    }

    /**
     * Set compteImpute
     *
     * @param string $compteImpute
     *
     * @return OI25_04
     */
    public function setCompteImpute($compteImpute)
    {
        $this->compteImpute = $compteImpute;
    
        return $this;
    }

    /**
     * Get compteImpute
     *
     * @return string 
     */
    public function getCompteImpute()
    {
        return $this->compteImpute;
    }

    /**
     * Set libelleComptable
     *
     * @param string $libelleComptable
     *
     * @return OI25_04
     */
    public function setLibelleComptable($libelleComptable)
    {
        $this->libelleComptable = $libelleComptable;
    
        return $this;
    }

    /**
     * Get libelleComptable
     *
     * @return string 
     */
    public function getLibelleComptable()
    {
        return $this->libelleComptable;
    }

    /**
     * Set montantImpute
     *
     * @param string $montantImpute
     *
     * @return OI25_04
     */
    public function setMontantImpute($montantImpute)
    {
        $this->montantImpute = $montantImpute;
    
        return $this;
    }

    /**
     * Get montantImpute
     *
     * @return string 
     */
    public function getMontantImpute()
    {
        return $this->montantImpute;
    }

    /**
     * Set deviseMontantImpute
     *
     * @param string $deviseMontantImpute
     *
     * @return OI25_04
     */
    public function setDeviseMontantImpute($deviseMontantImpute)
    {
        $this->deviseMontantImpute = $deviseMontantImpute;
    
        return $this;
    }

    /**
     * Get deviseMontantImpute
     *
     * @return string 
     */
    public function getDeviseMontantImpute()
    {
        return $this->deviseMontantImpute;
    }

    /**
     * Set nombreDecimalesDeviseImpute
     *
     * @param string $nombreDecimalesDeviseImpute
     *
     * @return OI25_04
     */
    public function setNombreDecimalesDeviseImpute($nombreDecimalesDeviseImpute)
    {
        $this->nombreDecimalesDeviseImpute = $nombreDecimalesDeviseImpute;
    
        return $this;
    }

    /**
     * Get nombreDecimalesDeviseImpute
     *
     * @return string 
     */
    public function getNombreDecimalesDeviseImpute()
    {
        return $this->nombreDecimalesDeviseImpute;
    }

    /**
     * Set typeMontantImpute
     *
     * @param string $typeMontantImpute
     *
     * @return OI25_04
     */
    public function setTypeMontantImpute($typeMontantImpute)
    {
        $this->typeMontantImpute = $typeMontantImpute;
    
        return $this;
    }

    /**
     * Get typeMontantImpute
     *
     * @return string 
     */
    public function getTypeMontantImpute()
    {
        return $this->typeMontantImpute;
    }

    /**
     * Set datePrelevementVirement
     *
     * @param string $datePrelevementVirement
     *
     * @return OI25_04
     */
    public function setDatePrelevementVirement($datePrelevementVirement)
    {
        $this->datePrelevementVirement = $datePrelevementVirement;
    
        return $this;
    }

    /**
     * Get datePrelevementVirement
     *
     * @return string 
     */
    public function getDatePrelevementVirement()
    {
        return $this->datePrelevementVirement;
    }

    /**
     * Set sensOperation
     *
     * @param string $sensOperation
     *
     * @return OI25_04
     */
    public function setSensOperation($sensOperation)
    {
        $this->sensOperation = $sensOperation;
    
        return $this;
    }

    /**
     * Get sensOperation
     *
     * @return string 
     */
    public function getSensOperation()
    {
        return $this->sensOperation;
    }

    /**
     * Set dateAchat
     *
     * @param string $dateAchat
     *
     * @return OI25_04
     */
    public function setDateAchat($dateAchat)
    {
        $this->dateAchat = $dateAchat;
    
        return $this;
    }

    /**
     * Get dateAchat
     *
     * @return string 
     */
    public function getDateAchat()
    {
        return $this->dateAchat;
    }

    /**
     * Set dateCompensation
     *
     * @param string $dateCompensation
     *
     * @return OI25_04
     */
    public function setDateCompensation($dateCompensation)
    {
        $this->dateCompensation = $dateCompensation;
    
        return $this;
    }

    /**
     * Get dateCompensation
     *
     * @return string 
     */
    public function getDateCompensation()
    {
        return $this->dateCompensation;
    }

    /**
     * Set montantAchatBrut
     *
     * @param string $montantAchatBrut
     *
     * @return OI25_04
     */
    public function setMontantAchatBrut($montantAchatBrut)
    {
        $this->montantAchatBrut = $montantAchatBrut;
    
        return $this;
    }

    /**
     * Get montantAchatBrut
     *
     * @return string 
     */
    public function getMontantAchatBrut()
    {
        return $this->montantAchatBrut;
    }

    /**
     * Set deviseMontantAchat
     *
     * @param string $deviseMontantAchat
     *
     * @return OI25_04
     */
    public function setDeviseMontantAchat($deviseMontantAchat)
    {
        $this->deviseMontantAchat = $deviseMontantAchat;
    
        return $this;
    }

    /**
     * Get deviseMontantAchat
     *
     * @return string 
     */
    public function getDeviseMontantAchat()
    {
        return $this->deviseMontantAchat;
    }

    /**
     * Set nombreDecimalesDeviseAchat
     *
     * @param string $nombreDecimalesDeviseAchat
     *
     * @return OI25_04
     */
    public function setNombreDecimalesDeviseAchat($nombreDecimalesDeviseAchat)
    {
        $this->nombreDecimalesDeviseAchat = $nombreDecimalesDeviseAchat;
    
        return $this;
    }

    /**
     * Get nombreDecimalesDeviseAchat
     *
     * @return string 
     */
    public function getNombreDecimalesDeviseAchat()
    {
        return $this->nombreDecimalesDeviseAchat;
    }

    /**
     * Set montantAchatDeviseOrigine
     *
     * @param string $montantAchatDeviseOrigine
     *
     * @return OI25_04
     */
    public function setMontantAchatDeviseOrigine($montantAchatDeviseOrigine)
    {
        $this->montantAchatDeviseOrigine = $montantAchatDeviseOrigine;
    
        return $this;
    }

    /**
     * Get montantAchatDeviseOrigine
     *
     * @return string 
     */
    public function getMontantAchatDeviseOrigine()
    {
        return $this->montantAchatDeviseOrigine;
    }

    /**
     * Set deviseOrigine
     *
     * @param string $deviseOrigine
     *
     * @return OI25_04
     */
    public function setDeviseOrigine($deviseOrigine)
    {
        $this->deviseOrigine = $deviseOrigine;
    
        return $this;
    }

    /**
     * Get deviseOrigine
     *
     * @return string 
     */
    public function getDeviseOrigine()
    {
        return $this->deviseOrigine;
    }

    /**
     * Set nombreDecimaleDeviseOrigine
     *
     * @param string $nombreDecimaleDeviseOrigine
     *
     * @return OI25_04
     */
    public function setNombreDecimaleDeviseOrigine($nombreDecimaleDeviseOrigine)
    {
        $this->nombreDecimaleDeviseOrigine = $nombreDecimaleDeviseOrigine;
    
        return $this;
    }

    /**
     * Get nombreDecimaleDeviseOrigine
     *
     * @return string 
     */
    public function getNombreDecimaleDeviseOrigine()
    {
        return $this->nombreDecimaleDeviseOrigine;
    }

    /**
     * Set montantCompenseEuro
     *
     * @param string $montantCompenseEuro
     *
     * @return OI25_04
     */
    public function setMontantCompenseEuro($montantCompenseEuro)
    {
        $this->montantCompenseEuro = $montantCompenseEuro;
    
        return $this;
    }

    /**
     * Get montantCompenseEuro
     *
     * @return string 
     */
    public function getMontantCompenseEuro()
    {
        return $this->montantCompenseEuro;
    }

    /**
     * Set deviseCompensation
     *
     * @param string $deviseCompensation
     *
     * @return OI25_04
     */
    public function setDeviseCompensation($deviseCompensation)
    {
        $this->deviseCompensation = $deviseCompensation;
    
        return $this;
    }

    /**
     * Get deviseCompensation
     *
     * @return string 
     */
    public function getDeviseCompensation()
    {
        return $this->deviseCompensation;
    }

    /**
     * Set nbDecimalesDeviseCompensation
     *
     * @param string $nbDecimalesDeviseCompensation
     *
     * @return OI25_04
     */
    public function setNbDecimalesDeviseCompensation($nbDecimalesDeviseCompensation)
    {
        $this->nbDecimalesDeviseCompensation = $nbDecimalesDeviseCompensation;
    
        return $this;
    }

    /**
     * Get nbDecimalesDeviseCompensation
     *
     * @return string 
     */
    public function getNbDecimalesDeviseCompensation()
    {
        return $this->nbDecimalesDeviseCompensation;
    }

    /**
     * Set montantCommissionsBanqueInt
     *
     * @param string $montantCommissionsBanqueInt
     *
     * @return OI25_04
     */
    public function setMontantCommissionsBanqueInt($montantCommissionsBanqueInt)
    {
        $this->montantCommissionsBanqueInt = $montantCommissionsBanqueInt;
    
        return $this;
    }

    /**
     * Get montantCommissionsBanqueInt
     *
     * @return string 
     */
    public function getMontantCommissionsBanqueInt()
    {
        return $this->montantCommissionsBanqueInt;
    }

    /**
     * Set deviseCommissionsBanque
     *
     * @param string $deviseCommissionsBanque
     *
     * @return OI25_04
     */
    public function setDeviseCommissionsBanque($deviseCommissionsBanque)
    {
        $this->deviseCommissionsBanque = $deviseCommissionsBanque;
    
        return $this;
    }

    /**
     * Get deviseCommissionsBanque
     *
     * @return string 
     */
    public function getDeviseCommissionsBanque()
    {
        return $this->deviseCommissionsBanque;
    }

    /**
     * Set nbDecimalesDeviseCompensation2
     *
     * @param string $nbDecimalesDeviseCompensation2
     *
     * @return OI25_04
     */
    public function setNbDecimalesDeviseCompensation2($nbDecimalesDeviseCompensation2)
    {
        $this->nbDecimalesDeviseCompensation2 = $nbDecimalesDeviseCompensation2;
    
        return $this;
    }

    /**
     * Get nbDecimalesDeviseCompensation2
     *
     * @return string 
     */
    public function getNbDecimalesDeviseCompensation2()
    {
        return $this->nbDecimalesDeviseCompensation2;
    }

    /**
     * Set montantCommissionsInterchange
     *
     * @param string $montantCommissionsInterchange
     *
     * @return OI25_04
     */
    public function setMontantCommissionsInterchange($montantCommissionsInterchange)
    {
        $this->montantCommissionsInterchange = $montantCommissionsInterchange;
    
        return $this;
    }

    /**
     * Get montantCommissionsInterchange
     *
     * @return string 
     */
    public function getMontantCommissionsInterchange()
    {
        return $this->montantCommissionsInterchange;
    }

    /**
     * Set deviseCommissionsBanque2
     *
     * @param string $deviseCommissionsBanque2
     *
     * @return OI25_04
     */
    public function setDeviseCommissionsBanque2($deviseCommissionsBanque2)
    {
        $this->deviseCommissionsBanque2 = $deviseCommissionsBanque2;
    
        return $this;
    }

    /**
     * Get deviseCommissionsBanque2
     *
     * @return string 
     */
    public function getDeviseCommissionsBanque2()
    {
        return $this->deviseCommissionsBanque2;
    }

    /**
     * Set nbDecimalesDeviseCompensation3
     *
     * @param string $nbDecimalesDeviseCompensation3
     *
     * @return OI25_04
     */
    public function setNbDecimalesDeviseCompensation3($nbDecimalesDeviseCompensation3)
    {
        $this->nbDecimalesDeviseCompensation3 = $nbDecimalesDeviseCompensation3;
    
        return $this;
    }

    /**
     * Get nbDecimalesDeviseCompensation3
     *
     * @return string 
     */
    public function getNbDecimalesDeviseCompensation3()
    {
        return $this->nbDecimalesDeviseCompensation3;
    }

    /**
     * Set referenceUnique
     *
     * @param string $referenceUnique
     *
     * @return OI25_04
     */
    public function setReferenceUnique($referenceUnique)
    {
        $this->referenceUnique = $referenceUnique;
    
        return $this;
    }

    /**
     * Get referenceUnique
     *
     * @return string 
     */
    public function getReferenceUnique()
    {
        return $this->referenceUnique;
    }

    /**
     * Set ARN
     *
     * @param string $aRN
     *
     * @return OI25_04
     */
    public function setARN($aRN)
    {
        $this->ARN = $aRN;
    
        return $this;
    }

    /**
     * Get ARN
     *
     * @return string 
     */
    public function getARN()
    {
        return $this->ARN;
    }

    /**
     * Set enseigneCommercant
     *
     * @param string $enseigneCommercant
     *
     * @return OI25_04
     */
    public function setEnseigneCommercant($enseigneCommercant)
    {
        $this->enseigneCommercant = $enseigneCommercant;
    
        return $this;
    }

    /**
     * Get enseigneCommercant
     *
     * @return string 
     */
    public function getEnseigneCommercant()
    {
        return $this->enseigneCommercant;
    }

    /**
     * Set siretCommercant
     *
     * @param string $siretCommercant
     *
     * @return OI25_04
     */
    public function setSiretCommercant($siretCommercant)
    {
        $this->siretCommercant = $siretCommercant;
    
        return $this;
    }

    /**
     * Get siretCommercant
     *
     * @return string 
     */
    public function getSiretCommercant()
    {
        return $this->siretCommercant;
    }

    /**
     * Set numeroContratCommercant
     *
     * @param string $numeroContratCommercant
     *
     * @return OI25_04
     */
    public function setNumeroContratCommercant($numeroContratCommercant)
    {
        $this->numeroContratCommercant = $numeroContratCommercant;
    
        return $this;
    }

    /**
     * Get numeroContratCommercant
     *
     * @return string 
     */
    public function getNumeroContratCommercant()
    {
        return $this->numeroContratCommercant;
    }

    /**
     * Set referenceClient
     *
     * @param string $referenceClient
     *
     * @return OI25_04
     */
    public function setReferenceClient($referenceClient)
    {
        $this->referenceClient = $referenceClient;
    
        return $this;
    }

    /**
     * Get referenceClient
     *
     * @return string 
     */
    public function getReferenceClient()
    {
        return $this->referenceClient;
    }

    /**
     * Set RIBTiers
     *
     * @param string $rIBTiers
     *
     * @return OI25_04
     */
    public function setRIBTiers($rIBTiers)
    {
        $this->RIBTiers = $rIBTiers;
    
        return $this;
    }

    /**
     * Get RIBTiers
     *
     * @return string 
     */
    public function getRIBTiers()
    {
        return $this->RIBTiers;
    }

    /**
     * Set RIBTiers2
     *
     * @param string $rIBTiers2
     *
     * @return OI25_04
     */
    public function setRIBTiers2($rIBTiers2)
    {
        $this->RIBTiers2 = $rIBTiers2;
    
        return $this;
    }

    /**
     * Get RIBTiers2
     *
     * @return string 
     */
    public function getRIBTiers2()
    {
        return $this->RIBTiers2;
    }
}