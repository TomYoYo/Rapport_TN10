<?php

namespace Fiscalite\ODBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table("OD_Action")
 * @ORM\Entity(repositoryClass="Fiscalite\ODBundle\Entity\ActionRepository")
 */
class Action
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_action", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idAction;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_action", type="datetime")
     */
    private $dateAction;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_action", type="string", length=20)
     */
    private $libelleAction;

    /**
     * @ORM\ManyToOne(targetEntity="\BackOffice\UserBundle\Entity\Profil", inversedBy="actions", cascade={"remove"})
     * @ORM\JoinColumn(name="login", referencedColumnName="id")
     */
    protected $profil;

    /**
     * @ORM\ManyToOne(targetEntity="Operation", inversedBy="actions", cascade={"remove"})
     * @ORM\JoinColumn(name="num_piece", referencedColumnName="num_piece", onDelete="CASCADE")
     */
    protected $operation;


    /**
     * Get idAction
     *
     * @return integer
     */
    public function getIdAction()
    {
        return $this->idAction;
    }

    /**
     * Set dateAction
     *
     * @return Action
     */
    public function setDateAction()
    {
        $this->dateAction = new \Datetime();

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \Date
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set libelleAction
     *
     * @param string $libelleAction
     * @return Action
     */
    public function setLibelleAction($libelleAction)
    {
        $this->libelleAction = $libelleAction;

        return $this;
    }

    /**
     * Get libelleAction
     *
     * @return string
     */
    public function getLibelleAction()
    {
        return $this->libelleAction;
    }

    /**
     * Set profil
     *
     * @param \BackOffice\UserBundle\Entity\Profi $profil
     * @return Action
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
     * Set operation
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $operation
     * @return Action
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
