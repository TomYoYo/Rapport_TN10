<?php

namespace Fiscalite\ODBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fiscalite\ODBundle\Entity\Action;

/**
 * Operation
 *
 * @ORM\Table("OD_Operation")
 * @ORM\Entity(repositoryClass="Fiscalite\ODBundle\Entity\OperationRepository")
 */
class Operation
{
    /**
     * @var string
     *
     * @ORM\Column(name="num_piece", type="string", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="OD_OPERATION_NUM_PIECE_SEQ", initialValue=1, allocationSize=1)
     */
    private $numPiece;
    
    /**
     * @var string
     *
     * @ORM\Column(name="num_piece_tech", type="integer", nullable=true)
     */
    private $numPieceTech;

    /**
     * @var string
     *
     * @ORM\Column(name="code_ope", type="string", length=3)
     */
    private $codeOpe;

    /**
     * @var string
     *
     * @ORM\Column(name="code_eve", type="string", length=3)
     */
    private $codeEve;

    /**
     * @var string
     *
     * @ORM\Column(name="tiers", type="string", length=7, nullable=true)
     */
    private $tiers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cpt", type="datetime")
     */
    private $dateCpt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_sai", type="datetime")
     */
    private $dateSai;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_valid", type="datetime", nullable=true)
     */
    private $dateValid;

    /**
     * @var string
     *
     * @ORM\Column(name="devise", type="string", length=3)
     */
    private $devise = "EUR";

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_val", type="datetime")
     */
    private $dateVal;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_let", type="string", length=7, nullable=true)
     */
    private $refLet;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_analytique", type="string", length=12, nullable=true)
     */
    private $refAnalytique;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_statut", type="datetime")
     */
    private $dateStatut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_statut_prec", type="datetime", nullable=true)
     */
    private $dateStatutPrec;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="string", length=1)
     */
    private $isDeleted = "0";

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_complementary_day", type="string", length=1, nullable=true)
     */
    private $isComplementaryDay = "0";

    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="operations", cascade={"persist"})
     * @ORM\JoinColumn(name="id_statut", referencedColumnName="id_statut")
     */
    protected $statut;

    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="operationsPrecedentes", cascade={"persist"})
     * @ORM\JoinColumn(name="id_statut_prec", referencedColumnName="id_statut")
     */
    protected $statutPrec;

    /**
     * @ORM\ManyToOne(targetEntity="\BackOffice\UserBundle\Entity\Profil", inversedBy="operations", cascade={"persist"})
     * @ORM\JoinColumn(name="saisisseur", referencedColumnName="id")
     */
    protected $profil;

    /**
     * @ORM\ManyToOne(targetEntity="\BackOffice\UserBundle\Entity\Profil", inversedBy="validOperations", cascade={"persist"})
     * @ORM\JoinColumn(name="valideur", referencedColumnName="id")
     */
    protected $valideur;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="operation", cascade={"persist"})
     */
    protected $actions;

    /**
     * @ORM\OneToMany(targetEntity="Mouvement", mappedBy="operation", cascade={"remove"})
     * @ORM\OrderBy({"numMvmt" = "ASC"})
     */
    protected $mouvements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mouvements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get numPiece
     *
     * @return string
     */
    public function getNumPiece()
    {
        return $this->numPiece;
    }
    
    /**
     * Set numPieceTech
     *
     * @param string $numPieceTech
     *
     * @return Operation
     */
    public function setNumPieceTech($numPieceTech)
    {
        $this->numPieceTech = $numPieceTech;

        return $this;
    }

    /**
     * Get numPieceTech
     *
     * @return string
     */
    public function getNumPieceTech()
    {
        return $this->numPieceTech;
    }

    /**
     * Set codeOpe
     *
     * @param string $codeOpe
     *
     * @return Operation
     */
    public function setCodeOpe($codeOpe)
    {
        $this->codeOpe = $codeOpe;

        return $this;
    }

    /**
     * Get codeOpe
     *
     * @return string
     */
    public function getCodeOpe()
    {
        return $this->codeOpe;
    }

    /**
     * Set codeEve
     *
     * @param string $codeEve
     *
     * @return Operation
     */
    public function setCodeEve($codeEve)
    {
        $this->codeEve = $codeEve;

        return $this;
    }

    /**
     * Get codeEve
     *
     * @return string
     */
    public function getCodeEve()
    {
        return $this->codeEve;
    }
    /*
     * Set tiers
     *
     * @param string $tiers
     *
     * @return Operation
     */
    public function setTiers($tiers)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return string
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set dateCpt
     *
     * @param \DateTime $dateCpt
     *
     * @return Operation
     */
    public function setDateCpt($dateCpt)
    {
        $this->dateCpt = $dateCpt;

        return $this;
    }

    /**
     * Get dateCpt
     *
     * @return \DateTime
     */
    public function getDateCpt()
    {
        return $this->dateCpt;
    }

    /**
     * Set dateSai
     *
     * @param \DateTime $dateSai
     *
     * @return Operation
     */
    public function setDateSai($dateSai)
    {
        $this->dateSai = $dateSai;

        return $this;
    }

    /**
     * Get dateSai
     *
     * @return \DateTime
     */
    public function getDateSai()
    {
        return $this->dateSai;
    }

    /**
     * Set devise
     *
     * @param string $devise
     *
     * @return Operation
     */
    public function setDevise($devise)
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return string
     */
    public function getDevise()
    {
        return $this->devise;
    }

    /**
     * Set dateVal
     *
     * @param \DateTime $dateVal
     *
     * @return Operation
     */
    public function setDateVal($dateVal)
    {
        $this->dateVal = $dateVal;

        return $this;
    }

    /**
     * Get dateVal
     *
     * @return \DateTime
     */
    public function getDateVal()
    {
        return $this->dateVal;
    }

    /**
     * Set refLet
     *
     * @param string $refLet
     *
     * @return Operation
     */
    public function setRefLet($refLet)
    {
        $this->refLet = $refLet;

        return $this;
    }

    /**
     * Get refLet
     *
     * @return string
     */
    public function getRefLet()
    {
        return $this->refLet;
    }

    /**
     * Set refAnalytique
     *
     * @param string $refAnalytique
     *
     * @return Operation
     */
    public function setRefAnalytique($refAnalytique)
    {
        $this->refAnalytique = $refAnalytique;

        return $this;
    }

    /**
     * Get refAnalytique
     *
     * @return string
     */
    public function getRefAnalytique()
    {
        return $this->refAnalytique;
    }

    /**
     * Set dateStatut
     *
     * @param \DateTime $dateStatut
     *
     * @return Operation
     */
    public function setDateStatut($dateStatut)
    {
        $this->dateStatut = $dateStatut;

        return $this;
    }

    /**
     * Get dateStatut
     *
     * @return \DateTime
     */
    public function getDateStatut()
    {
        return $this->dateStatut;
    }

    /**
     * Set dateStatutPrec
     *
     * @param \DateTime $dateStatutPrec
     *
     * @return Operation
     */
    public function setDateStatutPrec($dateStatutPrec)
    {
        $this->dateStatutPrec = $dateStatutPrec;

        return $this;
    }

    /**
     * Get dateStatutPrec
     *
     * @return \DateTime
     */
    public function getDateStatutPrec()
    {
        return $this->dateStatutPrec;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return Operation
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set isComplementaryDay
     *
     * @param boolean $isComplementaryDay
     *
     * @return Operation
     */
    public function setIsComplementaryDay($isComplementaryDay)
    {
        $this->isComplementaryDay = $isComplementaryDay;

        return $this;
    }

    /**
     * Get isComplementaryDay
     *
     * @return boolean
     */
    public function getIsComplementaryDay()
    {
        return $this->isComplementaryDay;
    }

    /**
     * Set statut
     *
     * @param \Fiscalite\ODBundle\Entity\Statut $statut
     *
     * @return Operation
     */
    public function setStatut(\Fiscalite\ODBundle\Entity\Statut $statut = null)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return \Fiscalite\ODBundle\Entity\Statut
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set statutPrec
     *
     * @param \Fiscalite\ODBundle\Entity\Statut $statutPrec
     *
     * @return Operation
     */
    public function setStatutPrec(\Fiscalite\ODBundle\Entity\Statut $statutPrec = null)
    {
        $this->statutPrec = $statutPrec;

        return $this;
    }

    /**
     * Get statutPrec
     *
     * @return \Fiscalite\ODBundle\Entity\Statut
     */
    public function getStatutPrec()
    {
        return $this->statutPrec;
    }

    /**
     * Set profil
     *
     * @param \BackOffice\UserBundle\Entity\Profil $profil
     *
     * @return Operation
     */
    public function setProfil(\BackOffice\UserBundle\Entity\Profil $profil = null)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get mouvements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMouvements()
    {
        return $this->mouvements;
    }

    public function reinitMvt()
    {
        $this->mouvements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->numPiece;
    }

    /**
     * Set dateValid
     *
     * @param \DateTime $dateValid
     *
     * @return Operation
     */
    public function setDateValid($dateValid)
    {
        $this->dateValid = $dateValid;
    
        return $this;
    }

    /**
     * Get dateValid
     *
     * @return \DateTime
     */
    public function getDateValid()
    {
        return $this->dateValid;
    }

    /**
     * Set valideur
     *
     * @param \BackOffice\UserBundle\Entity\Profil $valideur
     *
     * @return Operation
     */
    public function setValideur(\BackOffice\UserBundle\Entity\Profil $valideur = null)
    {
        $this->valideur = $valideur;
    
        return $this;
    }

    /**
     * Get valideur
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getValideur()
    {
        return $this->valideur;
    }

    /**
     * Add actions
     *
     * @param \Fiscalite\ODBundle\Entity\Action $actions
     *
     * @return Operation
     */
    public function addAction(\Fiscalite\ODBundle\Entity\Action $actions)
    {
        $this->actions[] = $actions;
    
        return $this;
    }

    /**
     * Remove actions
     *
     * @param \Fiscalite\ODBundle\Entity\Action $actions
     */
    public function removeAction(\Fiscalite\ODBundle\Entity\Action $actions)
    {
        $this->actions->removeElement($actions);
    }

    /**
     * Add mouvements
     *
     * @param \Fiscalite\ODBundle\Entity\Mouvement $mouvements
     *
     * @return Operation
     */
    public function addMouvement(\Fiscalite\ODBundle\Entity\Mouvement $mouvements)
    {
        $this->mouvements[] = $mouvements;
    
        return $this;
    }

    /**
     * Remove mouvements
     *
     * @param \Fiscalite\ODBundle\Entity\Mouvement $mouvements
     */
    public function removeMouvement(\Fiscalite\ODBundle\Entity\Mouvement $mouvements)
    {
        $this->mouvements->removeElement($mouvements);
    }
}
