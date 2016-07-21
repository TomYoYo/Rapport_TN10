<?php

namespace Editique\DATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BackOffice\ParserBundle\Manager\EcritureManager;

/**
 * PeriodeTaux
 *
 * @ORM\Table(name="ZDATCON0")
 * @ORM\Entity()
 */
class PeriodeTaux
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
     * @var integer
     *
     * @ORM\Column(name="DATCONDEB", type="integer")
     */
    private $dateDebut;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATCONFIN", type="integer")
     */
    private $dateFin;

    /**
     * @var float
     *
     * @ORM\Column(name="DATCONTXF", type="float")
     */
    private $taux;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATCONNUM", type="integer")
     */
    private $numOpe;

    /**
     * @var integer
     *
     * @ORM\Column(name="DATCONCON", type="integer")
     */
    private $num;

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
     * Set dateDebut
     *
     * @param integer $dateDebut
     * @return PeriodeTaux
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
        return EcritureManager::transformerDateSab2Human($this->dateDebut);
    }

    /**
     * Set dateFin
     *
     * @param integer $dateFin
     * @return PeriodeTaux
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return integer
     */
    public function getDateFin()
    {
        return EcritureManager::transformerDateSab2Human($this->dateFin);
    }

    /**
     * Set taux
     *
     * @param float $taux
     * @return PeriodeTaux
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

        return number_format($this->taux, 2, ',', ' ');
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return PeriodeTaux
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set numOpe
     *
     * @param integer $numOpe
     * @return PeriodeTaux
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
     * Set num
     *
     * @param integer $num
     * @return PeriodeTaux
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer
     */
    public function getNum()
    {
        return $this->num;
    }
}
