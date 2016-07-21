<?php

namespace BackOffice\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BackOffice\ActionBundle\Entity\Action;

/**
 * Action
 *
 * @ORM\Table(name="trigger_action")
 * @ORM\Entity(repositoryClass="BackOffice\ActionBundle\Entity\ActionRepository")
 */
class Action
{
    const SEUIL_NB_ESSAI = 10;

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=25)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="numCpt", type="string", length=30)
     */
    private $numCpt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtAction", type="datetime", nullable=true)
     */
    private $dtAction;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbEssai", type="integer")
     */
    private $nbEssai = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtDernierEssai", type="datetime", nullable=true)
     */
    private $dtDernierEssai;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="string")
     */
    private $etat = "attente";
    
    public function __construct()
    {
        $this->setDtAction(new \Datetime());
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
     * Set type
     *
     * @param string $type
     *
     * @return Action
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

    /**
     * Set module
     *
     * @param string $module
     *
     * @return Action
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set numCpt
     *
     * @param string $numCpt
     *
     * @return Action
     */
    public function setNumCpt($numCpt)
    {
        $this->numCpt = $numCpt;

        return $this;
    }

    /**
     * Get numCpt
     *
     * @return string
     */
    public function getNumCpt()
    {
        return $this->numCpt;
    }

    /**
     * Set dtAction
     *
     * @param \DateTime $dtAction
     *
     * @return Action
     */
    public function setDtAction($dtAction)
    {
        $this->dtAction = $dtAction;

        return $this;
    }

    /**
     * Get dtAction
     *
     * @return \DateTime
     */
    public function getDtAction()
    {
        return $this->dtAction;
    }

    /**
     * Set nbEssai
     *
     * @param integer $nbEssai
     *
     * @return Action
     */
    public function setNbEssai($nbEssai)
    {
        $this->nbEssai = $nbEssai;

        return $this;
    }

    /**
     * Get nbEssai
     *
     * @return integer
     */
    public function getNbEssai()
    {
        return $this->nbEssai;
    }

    /**
     * Set dtDernierEssai
     *
     * @param \DateTime $dtDernierEssai
     *
     * @return Action
     */
    public function setDtDernierEssai($dtDernierEssai)
    {
        $this->dtDernierEssai = $dtDernierEssai;

        return $this;
    }

    /**
     * Get dtDernierEssai
     *
     * @return \DateTime
     */
    public function getDtDernierEssai()
    {
        return $this->dtDernierEssai;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Action
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }
}
