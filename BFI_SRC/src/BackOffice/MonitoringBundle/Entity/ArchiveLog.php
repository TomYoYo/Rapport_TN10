<?php

namespace BackOffice\MonitoringBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BackOffice\UserBundle\Entity\Profil as Profil;

/**
 * Log
 *
 * @ORM\Table(name="archive_log")
 * @ORM\Entity(repositoryClass="BackOffice\MonitoringBundle\Entity\ArchiveLogRepository")
 */
class ArchiveLog
{
    const NIVEAU_INFO = 0;
    const NIVEAU_ALERT = 1;
    const NIVEAU_ERREUR = 2;
    const NIVEAU_SUCCESS = 3;

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
     * @var Profil
     *
     * @ORM\ManyToOne(targetEntity="BackOffice\UserBundle\Entity\Profil")
     */
    private $utilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=255, nullable= true)
     */
    private $module = '';

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable= true)
     */
    private $action = '';

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255)
     */
    private $niveau = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle = '';

    /**
     * On initialise le datetime dans le constructeur
     */
    public function __construct()
    {
        $this->datetime = new \DateTime();
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
     * @return Log
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
     * Set module
     *
     * @param string $module
     * @return Log
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
     * Set action
     *
     * @param string $action
     * @return Log
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
     * Set niveau
     *
     * @param string $niveau
     * @return Log
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Log
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

    public function getNiveauClassLibelle()
    {
        switch ($this->niveau) {
            case self::NIVEAU_INFO:
                return 'info';
            case self::NIVEAU_ALERT:
                return 'warning';
            case self::NIVEAU_ERREUR:
                return 'danger';
            case self::NIVEAU_SUCCESS:
                return 'success';
            default:
                return '';
        }
    }

    public function getNiveauLibelle()
    {
        switch ($this->niveau) {
            case self::NIVEAU_INFO:
                return 'Info';
            case self::NIVEAU_ALERT:
                return 'Alerte';
            case self::NIVEAU_ERREUR:
                return 'Erreur';
            case self::NIVEAU_SUCCESS:
                return 'Succès';
            default:
                return '';
        }
    }

    /**
     * Set utilisateur
     *
     * @param \BackOffice\UserBundle\Entity\Profil $utilisateur
     *
     * @return Log
     */
    public function setUtilisateur(\BackOffice\UserBundle\Entity\Profil $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
