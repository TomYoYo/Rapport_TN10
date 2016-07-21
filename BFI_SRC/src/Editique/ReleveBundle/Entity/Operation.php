<?php

namespace Editique\ReleveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Operation
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
     * @ORM\ManyToOne(targetEntity="Releve", inversedBy="tabOperation")
     */
    private $idReleve;

    /**
     * @var string
     *
     * @ORM\Column(name="dateOperation", type="string", length=10)
     */
    private $dateOperation;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="dateValeur", type="string", length=10)
     */
    private $dateValeur;

    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float")
     */
    private $debit;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float")
     */
    private $credit;


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
     * Set idOperation
     *
     * @param integer $idOperation
     * @return Operation
     */
    public function setIdOperation($idOperation)
    {
        $this->idOperation = $idOperation;

        return $this;
    }

    /**
     * Get idOperation
     *
     * @return integer
     */
    public function getIdOperation()
    {
        return $this->idOperation;
    }

    /**
     * Set dateOperation
     *
     * @param string $dateOperation
     * @return Operation
     */
    public function setDateOperation($dateOperation)
    {
        $this->dateOperation = $dateOperation;

        return $this;
    }

    /**
     * Get dateOperation
     *
     * @return string
     */
    public function getDateOperation()
    {
        return $this->dateOperation;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Operation
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
    
    public function getLibelleInArray()
    {
        return explode("\n", $this->libelle);
    }

    /**
     * Set dateValeur
     *
     * @param string $dateValeur
     * @return Operation
     */
    public function setDateValeur($dateValeur)
    {
        $this->dateValeur = $dateValeur;

        return $this;
    }

    /**
     * Get dateValeur
     *
     * @return string
     */
    public function getDateValeur()
    {
        return $this->dateValeur;
    }

    /**
     * Set debit
     *
     * @param float $debit
     * @return Operation
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param float $credit
     * @return Operation
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set idReleve
     *
     * @param \Editique\ReleveBundle\Entity\Releve $idReleve
     * @return Operation
     */
    public function setIdReleve(\Editique\ReleveBundle\Entity\Releve $idReleve = null)
    {
        $this->idReleve = $idReleve;

        return $this;
    }

    /**
     * Get idReleve
     *
     * @return \Editique\ReleveBundle\Entity\Releve
     */
    public function getIdReleve()
    {
        return $this->idReleve;
    }
}
