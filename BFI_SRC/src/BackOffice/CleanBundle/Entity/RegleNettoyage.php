<?php

namespace BackOffice\CleanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegleNettoyage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\CleanBundle\Entity\RegleNettoyageRepository")
 */
class RegleNettoyage
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
     * @ORM\Column(name="originServer", type="string", length=3)
     */
    private $originServer;

    /**
     * @var string
     *
     * @ORM\Column(name="originDir", type="string", length=50)
     */
    private $originDir;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=10)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="sousDossier", type="string", length=255, nullable=true)
     */
    private $sousDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="age", type="string", length=10, nullable=true)
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="destinationServer", type="string", length=3, nullable=true)
     */
    private $destinationServer;

    /**
     * @var string
     *
     * @ORM\Column(name="destinationDir", type="string", length=50, nullable=true)
     */
    private $destinationDir;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLaunch", type="datetime", nullable=true)
     */
    private $lastLaunch;

    /**
     * @var string
     *
     * @ORM\Column(name="lastResult", type="string", length=255, nullable=true)
     */
    private $lastResult;


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
     * Set originServer
     *
     * @param string $originServer
     *
     * @return RegleNettoyage
     */
    public function setOriginServer($originServer)
    {
        $this->originServer = $originServer;

        return $this;
    }

    /**
     * Get originServer
     *
     * @return string
     */
    public function getOriginServer()
    {
        return $this->originServer;
    }

    /**
     * Set originDir
     *
     * @param string $originDir
     *
     * @return RegleNettoyage
     */
    public function setOriginDir($originDir)
    {
        $this->originDir = $originDir;

        return $this;
    }

    /**
     * Get originDir
     *
     * @return string
     */
    public function getOriginDir()
    {
        return $this->originDir;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return RegleNettoyage
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set sousDossierv
     *
     * @param string $sousDossier
     *
     * @return RegleNettoyage
     */
    public function setSousDossier($sousDossier)
    {
        $this->sousDossier = $sousDossier;

        return $this;
    }

    /**
     * Get sousDossier
     *
     * @return string
     */
    public function getSousDossier()
    {
        return $this->sousDossier;
    }

    /**
     * Set string
     *
     * @param string $age
     *
     * @return RegleNettoyage
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    public function getAgeToString()
    {
        switch ($this->getAge()) {
            case '1mois':
                return '1 mois et 1 jour';
            case '2mois':
                return '2 mois et 1 jour';
            case '6mois':
                return '6 mois et 1 jour';
            case '14mois':
                return '14 mois et 1 jour';
        }

        return $this->age;
    }

    public function getAgeNbJour()
    {
        switch ($this->getAge()) {
            case '1mois':
                return '32';
            case '2mois':
                return '63';
            case '6mois':
                return '186';
            case '14mois':
                return '434';
        }

        return 62;
    }

    /**
     * Set destinationServer
     *
     * @param string $destinationServer
     *
     * @return RegleNettoyage
     */
    public function setDestinationServer($destinationServer)
    {
        $this->destinationServer = $destinationServer;

        return $this;
    }

    /**
     * Get destinationServer
     *
     * @return string
     */
    public function getDestinationServer()
    {
        return $this->destinationServer;
    }

    /**
     * Set destinationDir
     *
     * @param string $destinationDir
     *
     * @return RegleNettoyage
     */
    public function setDestinationDir($destinationDir)
    {
        $this->destinationDir = $destinationDir;

        return $this;
    }

    /**
     * Get destinationDir
     *
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->destinationDir;
    }

    /**
     * Set lastLaunch
     *
     * @param \DateTime $lastLaunch
     *
     * @return RegleNettoyage
     */
    public function setLastLaunch($lastLaunch)
    {
        $this->lastLaunch = $lastLaunch;

        return $this;
    }

    /**
     * Get lastLaunch
     *
     * @return \DateTime
     */
    public function getLastLaunch()
    {
        return $this->lastLaunch;
    }

    /**
     * Set lastResult
     *
     * @param string $lastResult
     *
     * @return RegleNettoyage
     */
    public function setLastResult($lastResult)
    {
        $this->lastResult = $lastResult;

        return $this;
    }

    /**
     * Get lastResult
     *
     * @return string
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }
}
