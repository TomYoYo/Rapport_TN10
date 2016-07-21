<?php

namespace Editique\CompteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Representant
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
     * @ORM\Column(name="idResp", type="string", length=7)
     */
    private $idResp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=4)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=32)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomJF", type="string", length=32)
     */
    private $nomJF;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=32)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="dateNai", type="string", length=32)
     */
    private $dateNai;

    /**
     * @var string
     *
     * @ORM\Column(name="villeNai", type="string", length=32)
     */
    private $villeNai;

    /**
     * @var string
     *
     * @ORM\Column(name="nationalite", type="string", length=30)
     */
    private $nationalite;

    /**
     * @var string
     *
     * @ORM\Column(name="adr1", type="string", length=32)
     */
    private $adr1;

    /**
     * @var string
     *
     * @ORM\Column(name="adr2", type="string", length=32)
     */
    private $adr2;

    /**
     * @var string
     *
     * @ORM\Column(name="adr3", type="string", length=32)
     */
    private $adr3;

    /**
     * @var string
     *
     * @ORM\Column(name="codPos", type="string", length=6)
     */
    private $codPos;

    /**
     * @var string
     *
     * @ORM\Column(name="vil", type="string", length=25)
     */
    private $vil;

    /**
     * @var string
     *
     * @ORM\Column(name="regimeMat", type="string", length=30)
     */
    private $regimeMat;

    /**
     * @var string
     *
     * @ORM\Column(name="telFixe", type="string", length=50)
     */
    private $telFixe;

    /**
     * @var string
     *
     * @ORM\Column(name="telPort", type="string", length=50)
     */
    private $telPort;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;
    
    
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
     * Set idResp
     *
     * @param string $idResp
     *
     * @return Compte
     */
    public function setIdResp($idResp)
    {
        $this->idResp = $idResp;
    
        return $this;
    }

    /**
     * Get idResp
     *
     * @return string
     */
    public function getIdResp()
    {
        return $this->idResp;
    }
    
    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return Compte
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;
    
        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Compte
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
        if (trim($this->nomJF)) {
            $nom = $this->nomJF . " épouse " . $this->nom;
        } else {
            $nom = $this->nom;
        }
        
        return array(
            substr($nom, 0, 60),
            substr($nom, 60, 12),
        );
    }

    /**
     * Set nomJF
     *
     * @param string $nomJF
     *
     * @return Compte
     */
    public function setNomJF($nomJF)
    {
        $this->nomJF = $nomJF;
    
        return $this;
    }

    /**
     * Get nomJF
     *
     * @return string
     */
    public function getNomJF()
    {
        return $this->nomJF;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Compte
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
        return trim($this->prenom);
    }

    /**
     * Set dateNai
     *
     * @param string $dateNai
     *
     * @return Compte
     */
    public function setDateNai($dateNai)
    {
        $this->dateNai = $dateNai;
    
        return $this;
    }

    /**
     * Get dateNai
     *
     * @return string
     */
    public function getDateNai()
    {
        $dateNai = $this->dateNai;
        
        if (strlen($dateNai) == 6) {
            $year = "19".substr($dateNai, 0, 2);
            $month = substr($dateNai, 2, 2);
            $day = substr($dateNai, 4, 2);
        } else {
            $year = substr($dateNai, 0, 4);
            $month = substr($dateNai, 4, 2);
            $day = substr($dateNai, 6, 2);
        }
        
        return $day.'/'.$month.'/'.$year;
    }

    /**
     * Set villeNai
     *
     * @param string $villeNai
     *
     * @return Compte
     */
    public function setVilleNai($villeNai)
    {
        $this->villeNai = $villeNai;
    
        return $this;
    }

    /**
     * Get villeNai
     *
     * @return string
     */
    public function getVilleNai()
    {
        return $this->villeNai;
    }

    /**
     * Set nationalite
     *
     * @param string $nationalite
     *
     * @return Compte
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;
    
        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }

    /**
     * Set adr1
     *
     * @param string $adr1
     *
     * @return Compte
     */
    public function setAdr1($adr1)
    {
        $this->adr1 = $adr1;
    
        return $this;
    }

    /**
     * Get adr1
     *
     * @return string
     */
    public function getAdr1()
    {
        return $this->adr1;
    }

    /**
     * Set adr2
     *
     * @param string $adr2
     *
     * @return Compte
     */
    public function setAdr2($adr2)
    {
        $this->adr2 = $adr2;
    
        return $this;
    }

    /**
     * Get adr2
     *
     * @return string
     */
    public function getAdr2()
    {
        return $this->adr2;
    }

    /**
     * Set adr3
     *
     * @param string $adr3
     *
     * @return Compte
     */
    public function setAdr3($adr3)
    {
        $this->adr3 = $adr3;
    
        return $this;
    }

    /**
     * Get adr3
     *
     * @return string
     */
    public function getAdr3()
    {
        return $this->adr3;
    }

    /**
     * Set codPos
     *
     * @param string $codPos
     *
     * @return Compte
     */
    public function setCodPos($codPos)
    {
        $this->codPos = $codPos;
    
        return $this;
    }

    /**
     * Get codPos
     *
     * @return string
     */
    public function getCodPos()
    {
        return $this->codPos;
    }

    /**
     * Set vil
     *
     * @param string $vil
     *
     * @return Compte
     */
    public function setVil($vil)
    {
        $this->vil = $vil;
    
        return $this;
    }

    /**
     * Get vil
     *
     * @return string
     */
    public function getVil()
    {
        return $this->vil;
    }
    
    public function getAdresseClient()
    {
        $i = 0;
        $adrClient = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (!trim($this->adr2) && !trim($this->adr3)) {
            $i++;
        }
        
        if (trim($this->adr1)) {
            $adrClient[$i] = $this->adr1;
            $i++;
        }
        
        if (trim($this->adr2)) {
            $adrClient[$i] = $this->adr2;
            $i++;
        }
        
        if (trim($this->adr3)) {
            $adrClient[$i] = $this->adr3;
            $i++;
        }
        
        $adrClient[$i] = $this->codPos . " " . $this->vil;
        
        return $adrClient;
    }

    /**
     * Set regimeMat
     *
     * @param string $regimeMat
     *
     * @return Compte
     */
    public function setRegimeMat($regimeMat)
    {
        $this->regimeMat = $regimeMat;
    
        return $this;
    }

    /**
     * Get regimeMat
     *
     * @return string
     */
    public function getRegimeMat()
    {
        return $this->regimeMat;
    }

    /**
     * Set telFixe
     *
     * @param string $telFixe
     *
     * @return Compte
     */
    public function setTelFixe($telFixe)
    {
        $this->telFixe = $telFixe;
    
        return $this;
    }

    /**
     * Get telFixe
     *
     * @return string
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * Set telPort
     *
     * @param string $telPort
     *
     * @return Compte
     */
    public function setTelPort($telPort)
    {
        $this->telPort = $telPort;
    
        return $this;
    }

    /**
     * Get telPort
     *
     * @return string
     */
    public function getTelPort()
    {
        return $this->telPort;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Compte
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
