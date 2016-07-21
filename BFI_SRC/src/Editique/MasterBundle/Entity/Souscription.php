<?php

namespace Editique\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Souscription
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Editique\MasterBundle\Entity\SouscriptionRepository")
 */
class Souscription
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
     * @ORM\Column(name="id_pret", type="string", length=255)
     */
    private $idPret;

    /**
     * @var string
     *
     * @ORM\Column(name="rcs", type="string", length=50)
     */
    private $rcs;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=255, nullable=true)
     */
    private $capital;

    /**
     * @var array
     *
     * @ORM\Column(name="dirigeants", type="array")
     */
    private $dirigeants;

    /**
     * @var string
     *
     * @ORM\Column(name="objet_fin", type="array")
     */
    private $objetFin;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dt_dec", type="string", nullable=true)
     */
    private $dtDec;
    
    /**
     * @var string
     *
     * @ORM\Column(name="diff_amo", type="string", nullable=true)
     */
    private $diffAmo;

    /**
     * @var string
     *
     * @ORM\Column(name="jour_pre", type="string")
     */
    private $jourPre;

    /**
     * @var string
     *
     * @ORM\Column(name="type_calcul", type="string", nullable=true)
     */
    private $typeCalcul;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ass1", type="string", length=69)
     */
    private $ass1;

    /**
     * @var string
     *
     * @ORM\Column(name="ass2", type="string", length=69, nullable=true)
     */
    private $ass2;

    /**
     * @var string
     *
     * @ORM\Column(name="ass3", type="string", length=69, nullable=true)
     */
    private $ass3;

    /**
     * @var string
     *
     * @ORM\Column(name="ass4", type="string", length=69, nullable=true)
     */
    private $ass4;

    /**
     * @var string
     *
     * @ORM\Column(name="ass5", type="string", length=69, nullable=true)
     */
    private $ass5;

    /**
     * @var array
     *
     * @ORM\Column(name="garanties", type="array")
     */
    private $garanties;

    /**
     * @var array
     *
     * @ORM\Column(name="fraisGar", type="array", nullable=true)
     */
    private $fraisGar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gar1", type="boolean", nullable=true)
     */
    private $gar1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gar2", type="boolean", nullable=true)
     */
    private $gar2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gar3", type="boolean", nullable=true)
     */
    private $gar3;

    /**
     * @var string
     *
     * @ORM\Column(name="comeng1", type="string", length=69, nullable=true)
     */
    private $comeng1;

    /**
     * @var string
     *
     * @ORM\Column(name="comeng2", type="string", length=69, nullable=true)
     */
    private $comeng2;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_exemplaire", type="string", nullable=true)
     */
    private $nombreExemplaire;

    /**
     * @var string
     *
     * @ORM\Column(name="description_ei", type="text", nullable=true)
     */
    private $descriptionEi;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_naissance", type="string", nullable=true)
     */
    private $villeNaissance;


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
     * Set idPret
     *
     * @param string $idPret
     *
     * @return Souscription
     */
    public function setIdPret($idPret)
    {
        $this->idPret = $idPret;
    
        return $this;
    }

    /**
     * Get idPret
     *
     * @return string
     */
    public function getIdPret()
    {
        return $this->idPret;
    }

    /**
     * Set rcs
     *
     * @param string $rcs
     *
     * @return Souscription
     */
    public function setRcs($rcs)
    {
        $this->rcs = $rcs;
    
        return $this;
    }

    /**
     * Get rcs
     *
     * @return string
     */
    public function getRcs()
    {
        return $this->rcs;
    }

    /**
     * Set capital
     *
     * @param string $capital
     *
     * @return Souscription
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;
    
        return $this;
    }

    /**
     * Get capital
     *
     * @return string
     */
    public function getCapital()
    {
        return number_format($this->capital, 2, ',', ' ');
    }

    /**
     * Set dirigeants
     *
     * @param array $dirigeants
     *
     * @return Souscription
     */
    public function setDirigeants($dirigeants)
    {
        $this->dirigeants = $dirigeants;
    
        return $this;
    }

    /**
     * Get dirigeants
     *
     * @return array
     */
    public function getDirigeants()
    {
        return $this->dirigeants;
    }

    /**
     * Set objetFin
     *
     * @param string $objetFin
     *
     * @return Souscription
     */
    public function setObjetFin($objetFin)
    {
        $this->objetFin = $objetFin;
    
        return $this;
    }

    /**
     * Get objetFin
     *
     * @return string
     */
    public function getObjetFin()
    {
        return $this->objetFin;
    }
    
    public function getObjetFin2()
    {
        $return = "";
        $numItems = count($this->objetFin);
        $i = 0;
        
        foreach ($this->objetFin as $line) {
            if (++$i !== $numItems) {
                $return .= $line."\n";
            } else {
                $return .= $line;
            }
        }
        
        return $return;
    }

    /**
     * Set ass1
     *
     * @param string $ass1
     *
     * @return Souscription
     */
    public function setAss1($ass1)
    {
        $this->ass1 = $ass1;
    
        return $this;
    }

    /**
     * Get ass1
     *
     * @return string
     */
    public function getAss1()
    {
        return $this->ass1;
    }

    /**
     * Set ass2
     *
     * @param string $ass2
     *
     * @return Souscription
     */
    public function setAss2($ass2)
    {
        $this->ass2 = $ass2;
    
        return $this;
    }

    /**
     * Get ass2
     *
     * @return string
     */
    public function getAss2()
    {
        return $this->ass2;
    }

    /**
     * Set ass3
     *
     * @param string $ass3
     *
     * @return Souscription
     */
    public function setAss3($ass3)
    {
        $this->ass3 = $ass3;
    
        return $this;
    }

    /**
     * Get ass3
     *
     * @return string
     */
    public function getAss3()
    {
        return $this->ass3;
    }

    /**
     * Set ass4
     *
     * @param string $ass4
     *
     * @return Souscription
     */
    public function setAss4($ass4)
    {
        $this->ass4 = $ass4;
    
        return $this;
    }

    /**
     * Get ass4
     *
     * @return string
     */
    public function getAss4()
    {
        return $this->ass4;
    }

    /**
     * Set ass5
     *
     * @param string $ass5
     *
     * @return Souscription
     */
    public function setAss5($ass5)
    {
        $this->ass5 = $ass5;
    
        return $this;
    }

    /**
     * Get ass5
     *
     * @return string
     */
    public function getAss5()
    {
        return $this->ass5;
    }

    /**
     * Set garanties
     *
     * @param array $garanties
     *
     * @return Souscription
     */
    public function setGaranties($garanties)
    {
        $this->garanties = $garanties;
    
        return $this;
    }

    /**
     * Get garanties
     *
     * @return array
     */
    public function getGaranties()
    {
        return $this->garanties;
    }
    
    public function getGaranties2()
    {
        $return = "";
        $numItems = count($this->garanties);
        $i = 0;
        
        foreach ($this->garanties as $line) {
            if (++$i !== $numItems) {
                $return .= $line."\n";
            } else {
                $return .= $line;
            }
        }
        
        return $return;
    }

    /**
     * Set gar1
     *
     * @param boolean $gar1
     *
     * @return Souscription
     */
    public function setGar1($gar1)
    {
        $this->gar1 = $gar1;
    
        return $this;
    }

    /**
     * Get gar1
     *
     * @return boolean
     */
    public function getGar1()
    {
        return $this->gar1;
    }

    /**
     * Set gar2
     *
     * @param boolean $gar2
     *
     * @return Souscription
     */
    public function setGar2($gar2)
    {
        $this->gar2 = $gar2;
    
        return $this;
    }

    /**
     * Get gar2
     *
     * @return boolean
     */
    public function getGar2()
    {
        return $this->gar2;
    }

    /**
     * Set gar3
     *
     * @param boolean $gar3
     *
     * @return Souscription
     */
    public function setGar3($gar3)
    {
        $this->gar3 = $gar3;
    
        return $this;
    }

    /**
     * Get gar3
     *
     * @return boolean
     */
    public function getGar3()
    {
        return $this->gar3;
    }

    /**
     * Set dtDec
     *
     * @param string $dtDec
     *
     * @return Souscription
     */
    public function setDtDec($dtDec)
    {
        $this->dtDec = $dtDec;
    
        return $this;
    }

    /**
     * Get dtDec
     *
     * @return string
     */
    public function getDtDec()
    {
        return $this->dtDec;
    }

    /**
     * Set diffAmo
     *
     * @param string $diffAmo
     *
     * @return Souscription
     */
    public function setDiffAmo($diffAmo)
    {
        $this->diffAmo = $diffAmo;
    
        return $this;
    }

    /**
     * Get diffAmo
     *
     * @return string
     */
    public function getDiffAmo()
    {
        return $this->diffAmo;
    }

    /**
     * Set jourPre
     *
     * @param string $jourPre
     *
     * @return Souscription
     */
    public function setJourPre($jourPre)
    {
        $this->jourPre = $jourPre;
    
        return $this;
    }

    /**
     * Get jourPre
     *
     * @return string
     */
    public function getJourPre()
    {
        return $this->jourPre;
    }

    /**
     * Set typeCalcul
     *
     * @param string $typeCalcul
     *
     * @return Souscription
     */
    public function setTypeCalcul($typeCalcul)
    {
        $this->typeCalcul = $typeCalcul;
    
        return $this;
    }

    /**
     * Get typeCalcul
     *
     * @return string
     */
    public function getTypeCalcul()
    {
        return $this->typeCalcul;
    }

    /**
     * Set nombreExemplaire
     *
     * @param string $nombreExemplaire
     *
     * @return Souscription
     */
    public function setNombreExemplaire($nombreExemplaire)
    {
        $this->nombreExemplaire = $nombreExemplaire;
    
        return $this;
    }

    /**
     * Get nombreExemplaire
     *
     * @return string
     */
    public function getNombreExemplaire()
    {
        return $this->nombreExemplaire;
    }

    /**
     * Set descriptionEi
     *
     * @param string $descriptionEi
     *
     * @return Souscription
     */
    public function setDescriptionEi($descriptionEi)
    {
        $this->descriptionEi = $descriptionEi;
    
        return $this;
    }

    /**
     * Get nombreExemplaire
     *
     * @return string
     */
    public function getDescriptionEi()
    {
        return $this->descriptionEi;
    }

    /**
     * Set villeNaissance
     *
     * @param string $villeNaissance
     *
     * @return Souscription
     */
    public function setVilleNaissance($villeNaissance)
    {
        $this->villeNaissance = $villeNaissance;
    
        return $this;
    }

    /**
     * Get villeNaissance
     *
     * @return string
     */
    public function getVilleNaissance()
    {
        return $this->villeNaissance;
    }

    /**
     * Set comeng1
     *
     * @param string $comeng1
     *
     * @return Souscription
     */
    public function setComeng1($comeng1)
    {
        $this->comeng1 = $comeng1;
    
        return $this;
    }

    /**
     * Get comeng1
     *
     * @return string
     */
    public function getComeng1()
    {
        return $this->comeng1;
    }

    /**
     * Set comeng2
     *
     * @param string $comeng2
     *
     * @return Souscription
     */
    public function setComeng2($comeng2)
    {
        $this->comeng2 = $comeng2;
    
        return $this;
    }

    /**
     * Get comeng2
     *
     * @return string
     */
    public function getComeng2()
    {
        return $this->comeng2;
    }

    /**
     * Set fraisGar
     *
     * @param array $fraisGar
     *
     * @return Souscription
     */
    public function setFraisGar($fraisGar)
    {
        $this->fraisGar = $fraisGar;
    
        return $this;
    }

    /**
     * Get fraisGar
     *
     * @return array 
     */
    public function getFraisGar()
    {
        return $this->fraisGar;
    }
}
