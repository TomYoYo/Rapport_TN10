<?php

namespace BackOffice\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profil
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\UserBundle\Entity\ProfilRepository")
 */
class Profil extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=30)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="code_user", type="string", length=4)
     */
    private $codeUser;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=20)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=20)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_enr", type="datetime")
     */
    private $dateEnr;

    /**
     * @var string
     *
     * @ORM\Column(name="code_etabl", type="string", length=4)
     */
    private $codeEtabl;

    /**
     * @var string
     *
     * @ORM\Column(name="code_agence", type="string", length=4)
     */
    private $codeAgence;

    /**
     * @var string
     *
     * @ORM\Column(name="code_service", type="string", length=2)
     */
    private $codeService;

    /**
     * @var string
     *
     * @ORM\Column(name="code_ss_service", type="string", length=2)
     */
    private $codeSsService;
    
    /**
     * @var array
     *
     * @ORM\Column(name="notifications", type="array", nullable=true)
     */
    private $notifications;
    
    /**
     * @ORM\OneToMany(targetEntity="\Fiscalite\ODBundle\Entity\Operation", mappedBy="profil", cascade={"remove", "persist"})
     */
    protected $operations;
    
    /**
     * @ORM\OneToMany(targetEntity="\Fiscalite\ODBundle\Entity\Operation", mappedBy="valideur", cascade={"remove", "persist"})
     */
    protected $validOperations;
   
    /**
     * @ORM\OneToMany(targetEntity="\Fiscalite\ODBundle\Entity\Action", mappedBy="profil", cascade={"remove", "persist"})
     */
    protected $actions;

    /**
     * Set login
     *
     * @param string $login
     * @return Profil
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set codeUser
     *
     * @param string $codeUser
     * @return Profil
     */
    public function setCodeUser($codeUser)
    {
        $this->codeUser = $codeUser;

        return $this;
    }

    /**
     * Get codeUser
     *
     * @return string
     */
    public function getCodeUser()
    {
        return $this->codeUser;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Profil
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Profil
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateEnr
     *
     * @return Profil
     */
    public function setDateEnr()
    {
        $this->dateEnr = new \Datetime();

        return $this;
    }

    /**
     * Get dateEnr
     *
     * @return \DateTime
     */
    public function getDateEnr()
    {
        return $this->dateEnr;
    }

    /**
     * Set codeEtabl
     *
     * @param string $codeEtabl
     * @return Profil
     */
    public function setCodeEtabl($codeEtabl)
    {
        $this->codeEtabl = $codeEtabl;

        return $this;
    }

    /**
     * Get codeEtabl
     *
     * @return string
     */
    public function getCodeEtabl()
    {
        return $this->codeEtabl;
    }

    /**
     * Set codeAgence
     *
     * @param string $codeAgence
     * @return Profil
     */
    public function setCodeAgence($codeAgence)
    {
        $this->codeAgence = $codeAgence;

        return $this;
    }

    /**
     * Get codeAgence
     *
     * @return string
     */
    public function getCodeAgence()
    {
        return $this->codeAgence;
    }

    /**
     * Set codeService
     *
     * @param string $codeService
     * @return Profil
     */
    public function setCodeService($codeService)
    {
        $this->codeService = $codeService;

        return $this;
    }

    /**
     * Get codeService
     *
     * @return string
     */
    public function getCodeService()
    {
        return $this->codeService;
    }

    /**
     * Set codeSsService
     *
     * @param string $codeSsService
     * @return Profil
     */
    public function setCodeSsService($codeSsService)
    {
        $this->codeSsService = $codeSsService;

        return $this;
    }

    /**
     * Get codeSsService
     *
     * @return string
     */
    public function getCodeSsService()
    {
        return $this->codeSsService;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->setDateEnr();
    }

    /**
     * Add operations
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $operations
     * @return Profil
     */
    public function addOperation(\Fiscalite\ODBundle\Entity\Operation $operations)
    {
        $this->operations[] = $operations;

        return $this;
    }

    /**
     * Remove operations
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $operations
     */
    public function removeOperation(\Fiscalite\ODBundle\Entity\Operation $operations)
    {
        $this->operations->removeElement($operations);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Add actions
     *
     * @param \Fiscalite\ODBundle\Entity\Action $actions
     * @return Profil
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
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    public function __toString()
    {
        return $this->login;
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
     * Set notifications
     *
     * @param array $notifications
     *
     * @return Profil
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    
        return $this;
    }

    /**
     * Get notifications
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add validOperations
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $validOperations
     *
     * @return Profil
     */
    public function addValidOperation(\Fiscalite\ODBundle\Entity\Operation $validOperations)
    {
        $this->validOperations[] = $validOperations;
    
        return $this;
    }

    /**
     * Remove validOperations
     *
     * @param \Fiscalite\ODBundle\Entity\Operation $validOperations
     */
    public function removeValidOperation(\Fiscalite\ODBundle\Entity\Operation $validOperations)
    {
        $this->validOperations->removeElement($validOperations);
    }

    /**
     * Get validOperations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValidOperations()
    {
        return $this->validOperations;
    }
}
