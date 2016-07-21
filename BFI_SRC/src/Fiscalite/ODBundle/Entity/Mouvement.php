<?php

namespace Fiscalite\ODBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table("OD_Mouvement")
 * @ORM\Entity()
 */
class Mouvement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_ope", type="string", length=15)
     * @ORM\Id
     */
    private $idOpe;
   
    /**
     * @var string
     *
     * @ORM\Column(name="num_piece", type="string", length=6)
     */
    private $numPiece;
    
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="num_mvmt", type="integer")
     */
    private $numMvmt;

    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=20)
     */
    protected $compte;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="code_budget", type="string", length=4, nullable=true)
     */
    private $codeBudget;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=60, nullable=true)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity="Operation", inversedBy="mouvements", cascade={"persist"})
     * @ORM\JoinColumn(name="num_piece", referencedColumnName="num_piece", onDelete="CASCADE")
     */
    protected $operation;
    

    

    /**
     * Set idOpe
     *
     * @param string $idOpe
     *
     * @return Mouvement
     */
    public function setIdOpe($idOpe)
    {
        $this->idOpe = $idOpe;

        return $this;
    }
    
    
    
    /**
     * Get idOpe
     *
     * @return string
     */
    public function getIdOpe()
    {
        return $this->idOpe;
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
     * Set numMvmt
     *
     * @param integer $numMvmt
     *
     * @return Mouvement
     */
    public function setNumMvmt($numMvmt)
    {
        $this->numMvmt = $numMvmt;

        return $this;
    }

    /**
     * Get numMvmt
     *
     * @return integer
     */
    public function getNumMvmt()
    {
        return $this->numMvmt;
    }

    /**
     * Set compte
     *
     * @param string $compte
     *
     * @return Mouvement
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return Mouvement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set codeBudget
     *
     * @param string $codeBudget
     *
     * @return Mouvement
     */
    public function setCodeBudget($codeBudget)
    {
        $this->codeBudget = $codeBudget;

        return $this;
    }

    /**
     * Get codeBudget
     *
     * @return string
     */
    public function getCodeBudget()
    {
        return $this->codeBudget;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Mouvement
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set operation
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $operation
     *
     * @return Mouvement
     */
    public function setOperation(\Fiscalite\ODBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \Fiscalite\ODBundle\Entity\Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }
}
