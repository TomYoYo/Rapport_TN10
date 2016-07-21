<?php

namespace Editique\CreditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Echeance
 *
 * @ORM\Table(name="ZCREBIS0")
 * @ORM\Entity(repositoryClass="Editique\CreditBundle\Entity\EcheanceRepository")
 */
class Echeance
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
     * @ORM\Column(name="CREBISECH", type="string", length=6)
     */
    private $numEcheance;

    /**
     * @var integer
     *
     * @ORM\Column(name="CREBISMAM", type="string", length=18)
     */
    private $capAmorti;

    /**
     * @var integer
     *
     * @ORM\Column(name="CREBISMIN", type="string", length=18)
     */
    private $interetEch;

    /**
     * @var float
     * //ORM\Column(type="float")
     */
    private $montantHorsAss = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="CREBISASC", type="string", length=18)
     */
    private $montantAss;

    /**
     * @var integer
     *
     * @ORM\Column(name="CREBISMRE", type="string", length=18)
     */
    private $totalEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISDEB", type="string", length=7)
     */
    private $debutEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISFIN", type="string", length=7)
     */
    private $finEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISDTR", type="string", length=7)
     */
    private $datePlvt;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISPRE", type="string", length=6)
     */
    private $numPret;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISDOS", type="string", length=7)
     */
    private $numDos;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISCAS", type="string", length=6)
     */
    private $numCas;

    /**
     * @var string
     *
     * @ORM\Column(name="CREBISTYP", type="string")
     */
    private $type;

    /**
     * /////ORM\ManyToOne(targetEntity="Credit", inversedBy="echeances")
     */
    private $credit;

    /**
     *
     * @var float
     *
     */
    private $capRestDu;


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
     * Set numEcheance
     *
     * @param string $numEcheance
     *
     * @return Echeance
     */
    public function setNumEcheance($numEcheance)
    {
        $this->numEcheance = $numEcheance;

        return $this;
    }

    /**
     * Get numEcheance
     *
     * @return string
     */
    public function getNumEcheance()
    {
        return (int)$this->numEcheance;
    }

    /**
     * Set capAmorti
     *
     * @param string $capAmorti
     *
     * @return Echeance
     */
    public function setCapAmorti($capAmorti)
    {
        $this->capAmorti = $capAmorti;

        return $this;
    }

    /**
     * Get capAmorti
     *
     * @return string
     */
    public function getCapAmorti()
    {
        return $this->capAmorti;
    }

    /**
     * Set interetEch
     *
     * @param string $interetEch
     *
     * @return Echeance
     */
    public function setInteretEch($interetEch)
    {
        $this->interetEch = $interetEch;

        return $this;
    }

    /**
     * Get interetEch
     *
     * @return string
     */
    public function getInteretEch()
    {
        return $this->interetEch;
    }

    /**
     * Set montantAss
     *
     * @param string $montantAss
     *
     * @return Echeance
     */
    public function setMontantAss($montantAss)
    {
        $this->montantAss = $montantAss;

        return $this;
    }

    /**
     * Get montantAss
     *
     * @return string
     */
    public function getMontantAss()
    {
        return $this->montantAss;
    }

    /**
     * Set totalEcheance
     *
     * @param string $totalEcheance
     *
     * @return Echeance
     */
    public function setTotalEcheance($totalEcheance)
    {
        $this->totalEcheance = $totalEcheance;

        return $this;
    }

    /**
     * Get totalEcheance
     *
     * @return string
     */
    public function getTotalEcheance()
    {
        return $this->totalEcheance;
    }

    /**
     * Set credit
     *
     * @param \Editique\CreditBundle\Entity\Credit $credit
     * @return Operation
     */
    public function setCredit(\Editique\CreditBundle\Entity\Credit $credit = null)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return \Editique\CreditBundle\Entity\Credit
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set numPret
     *
     * @param string $numPret
     *
     * @return Echeance
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
     * @return Echeance
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
     * Set numCas
     *
     * @param string $numCas
     *
     * @return Echeance
     */
    public function setNumCas($numCas)
    {
        $this->numCas = $numCas;

        return $this;
    }

    /**
     * Get numCas
     *
     * @return string
     */
    public function getNumCas()
    {
        return $this->numCas;
    }

    /**
     * Set capRestDu
     *
     * @param float $capRestDu
     *
     * @return Echeance
     */
    public function setCapRestDu($capRestDu)
    {
        $this->capRestDu = $capRestDu;

        return $this;
    }

    /**
     * Get capRestDu
     *
     * @return float
     */
    public function getCapRestDu()
    {
        return $this->capRestDu;
    }

    /**
     * Set montantHorsAss
     *
     * @param float $montantHorsAss
     *
     * @return Echeance
     */
    public function setMontantHorsAss($montantHorsAss)
    {
        $this->montantHorsAss = $montantHorsAss;

        return $this;
    }

    /**
     * Get montantHorsAss
     *
     * @return float
     */
    public function getMontantHorsAss()
    {
        return $this->montantHorsAss;
    }

    /**
     * Set debutEcheance
     *
     * @param string $debutEcheance
     *
     * @return Echeance
     */
    public function setDebutEcheance($debutEcheance)
    {
        $this->debutEcheance = $debutEcheance;

        return $this;
    }

    /**
     * Get debutEcheance
     *
     * @return string
     */
    public function getDebutEcheance()
    {
        return $this->debutEcheance;
    }

    public function getDateTimeDebutEcheance()
    {
        $date = date_create_from_format("1ymd", $this->debutEcheance);
        return date_format($date, 'd/m/Y');
    }

    /**
     * Set finEcheance
     *
     * @param string $finEcheance
     *
     * @return Echeance
     */
    public function setFinEcheance($finEcheance)
    {
        $this->finEcheance = $finEcheance;

        return $this;
    }

    /**
     * Get finEcheance
     *
     * @return string
     */
    public function getFinEcheance()
    {
        return $this->finEcheance;
    }

    public function getDateTimeFinEcheance()
    {
        $date = date_create_from_format("1ymd", $this->finEcheance);
        return date_format($date, 'd/m/Y');
    }

    /**
     * Set datePlvt
     *
     * @param string $datePlvt
     *
     * @return Echeance
     */
    public function setDatePlvt($datePlvt)
    {
        $this->datePlvt = $datePlvt;

        return $this;
    }

    /**
     * Get datePlvt
     *
     * @return string
     */
    public function getDatePlvt()
    {
        return $this->datePlvt;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Echeance
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
