<?php

namespace BackOffice\MonitoringBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\MonitoringBundle\Entity\MailRepository")
 */
class Mail
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
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_log", type="string", length=255)
     */
    private $libelleLog;
    
    /**
     * @var string
     *
     * @ORM\Column(name="action_log", type="string", length=255, nullable=true)
     */
    private $actionLog;
    
    /**
     * @var string
     *
     * @ORM\Column(name="module_log", type="string", length=255)
     */
    private $moduleLog;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sended", type="boolean")
     */
    private $isSended = false;
    
    /**
    * @ORM\OneToOne(targetEntity="Log")
    * @ORM\JoinColumn(name="log", referencedColumnName="id")
    */
    private $log;

    public function __construct()
    {
        $this
            ->setDatetime(new \Datetime());
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
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Mail
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set isSended
     *
     * @param boolean $isSended
     *
     * @return Mail
     */
    public function setIsSended($isSended)
    {
        $this->isSended = $isSended;
    
        return $this;
    }

    /**
     * Get isSended
     *
     * @return boolean
     */
    public function getIsSended()
    {
        return $this->isSended;
    }

    /**
     * Set log
     *
     * @param \BackOffice\MonitoringBundle\Entity\Log $log
     *
     * @return Mail
     */
    public function setLog(\BackOffice\MonitoringBundle\Entity\Log $log = null)
    {
        $this->log = $log;
    
        return $this;
    }

    /**
     * Get log
     *
     * @return \BackOffice\MonitoringBundle\Entity\Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set libelleLog
     *
     * @param string $libelleLog
     *
     * @return Mail
     */
    public function setLibelleLog($libelleLog)
    {
        $this->libelleLog = $libelleLog;
    
        return $this;
    }

    /**
     * Get libelleLog
     *
     * @return string
     */
    public function getLibelleLog()
    {
        return $this->libelleLog;
    }

    /**
     * Set actionLog
     *
     * @param string $actionLog
     *
     * @return Mail
     */
    public function setActionLog($actionLog)
    {
        $this->actionLog = $actionLog;
    
        return $this;
    }

    /**
     * Get actionLog
     *
     * @return string
     */
    public function getActionLog()
    {
        return $this->actionLog;
    }

    /**
     * Set moduleLog
     *
     * @param string $moduleLog
     *
     * @return Mail
     */
    public function setModuleLog($moduleLog)
    {
        $this->moduleLog = $moduleLog;
    
        return $this;
    }

    /**
     * Get moduleLog
     *
     * @return string
     */
    public function getModuleLog()
    {
        return $this->moduleLog;
    }
}
