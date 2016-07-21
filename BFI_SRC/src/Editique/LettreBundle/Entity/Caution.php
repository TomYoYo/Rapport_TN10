<?php

namespace Editique\LettreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caution
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Caution
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
     * @ORM\Column(name="type", type="string", length=5)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="idClientCaution", type="string", length=7)
     */
    private $idClientCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="idClientPret", type="string", length=7)
     */
    private $idClientPret;

    /**
     * @var string
     *
     * @ORM\Column(name="idCaution", type="string", length=7)
     */
    private $idCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="ra1ClientCaution", type="string", length=38)
     */
    private $ra1ClientCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="ra2ClientCaution", type="string", length=38)
     */
    private $ra2ClientCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="ra1ClientPret", type="string", length=38)
     */
    private $ra1ClientPret;

    /**
     * @var string
     *
     * @ORM\Column(name="ra2ClientPret", type="string", length=38)
     */
    private $ra2ClientPret;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseCaution1", type="string", length=38)
     */
    private $adresseCaution1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseCaution2", type="string", length=38)
     */
    private $adresseCaution2;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseCaution3", type="string", length=38)
     */
    private $adresseCaution3;

    /**
     * @var string
     *
     * @ORM\Column(name="cpCaution", type="string", length=12)
     */
    private $cpCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="villeCaution", type="string", length=32)
     */
    private $villeCaution;

    /**
     * @var string
     *
     * @ORM\Column(name="adressePret1", type="string", length=38)
     */
    private $adressePret1;

    /**
     * @var string
     *
     * @ORM\Column(name="adressePret2", type="string", length=38)
     */
    private $adressePret2;

    /**
     * @var string
     *
     * @ORM\Column(name="adressePret3", type="string", length=38)
     */
    private $adressePret3;

    /**
     * @var string
     *
     * @ORM\Column(name="cpPret", type="string", length=12)
     */
    private $cpPret;

    /**
     * @var string
     *
     * @ORM\Column(name="villePret", type="string", length=32)
     */
    private $villePret;

    /**
     * @var string
     *
     * @ORM\Column(name="dateSituation", type="string", length=10)
     */
    private $dateSituation;

    /**
     * @var float
     *
     * @ORM\Column(name="mtCautionInit", type="float")
     */
    private $mtCautionInit;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFin", type="string", length=10)
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="idNumDossierPret", type="string", length=9, nullable=true)
     */
    private $idNumDossierPret;

    /**
     * @var string
     *
     * @ORM\Column(name="idDossier", type="string", length=7, nullable=true)
     */
    private $idDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="idPret", type="string", length=2, nullable=true)
     */
    private $idPret;

    /**
     * @var float
     *
     * @ORM\Column(name="mtPretInit", type="float", nullable=true)
     */
    private $mtPretInit;

    /**
     * @var float
     *
     * @ORM\Column(name="taux", type="float", nullable=true)
     */
    private $taux;

    /**
     * @var float
     *
     * @ORM\Column(name="mtPretRestDu", type="float", nullable=true)
     */
    private $mtPretRestDu;

    /**
     * @var float
     *
     * @ORM\Column(name="mtInteret", type="float", nullable=true)
     */
    private $mtInteret;

    /**
     * @var string
     *
     * @ORM\Column(name="libCodeTaux", type="string", length=100, nullable=true)
     */
    private $libCodeTaux;

    /**
     * @var float
     *
     * @ORM\Column(name="valCodeTaux", type="float", nullable=true)
     */
    private $valCodeTaux;

    /**
     * @var float
     *
     * @ORM\Column(name="margeTaux", type="float", nullable=true)
     */
    private $margeTaux;

    /**
     * @var float
     *
     * @ORM\Column(name="mtEngagementInit", type="float", nullable=true)
     */
    private $mtEngagementInit;

    /**
     * @var string
     *
     * @ORM\Column(name="typeImpression", type="string", length=10, nullable=true)
     */
    private $typeImpression;

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
     * @return Caution
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
     * Set idClientCaution
     *
     * @param string $idClientCaution
     *
     * @return Caution
     */
    public function setIdClientCaution($idClientCaution)
    {
        $this->idClientCaution = $idClientCaution;
    
        return $this;
    }

    /**
     * Get idClientCaution
     *
     * @return string 
     */
    public function getIdClientCaution()
    {
        return $this->idClientCaution;
    }

    /**
     * Set idClientPret
     *
     * @param string $idClientPret
     *
     * @return Caution
     */
    public function setIdClientPret($idClientPret)
    {
        $this->idClientPret = $idClientPret;
    
        return $this;
    }

    /**
     * Get idClientPret
     *
     * @return string 
     */
    public function getIdClientPret()
    {
        return $this->idClientPret;
    }

    /**
     * Set idCaution
     *
     * @param string $idCaution
     *
     * @return Caution
     */
    public function setIdCaution($idCaution)
    {
        $this->idCaution = $idCaution;
    
        return $this;
    }

    /**
     * Get idCaution
     *
     * @return string 
     */
    public function getIdCaution()
    {
        return $this->idCaution;
    }

    /**
     * Set ra1ClientCaution
     *
     * @param string $ra1ClientCaution
     *
     * @return Caution
     */
    public function setRa1ClientCaution($ra1ClientCaution)
    {
        $this->ra1ClientCaution = $ra1ClientCaution;
    
        return $this;
    }

    /**
     * Get ra1ClientCaution
     *
     * @return string 
     */
    public function getRa1ClientCaution()
    {
        return $this->ra1ClientCaution;
    }

    /**
     * Set ra2ClientCaution
     *
     * @param string $ra2ClientCaution
     *
     * @return Caution
     */
    public function setRa2ClientCaution($ra2ClientCaution)
    {
        $this->ra2ClientCaution = $ra2ClientCaution;
    
        return $this;
    }

    /**
     * Get ra2ClientCaution
     *
     * @return string 
     */
    public function getRa2ClientCaution()
    {
        return $this->ra2ClientCaution;
    }

    /**
     * Set ra1ClientPret
     *
     * @param string $ra1ClientPret
     *
     * @return Caution
     */
    public function setRa1ClientPret($ra1ClientPret)
    {
        $this->ra1ClientPret = $ra1ClientPret;
    
        return $this;
    }

    /**
     * Get ra1ClientPret
     *
     * @return string 
     */
    public function getRa1ClientPret()
    {
        return $this->ra1ClientPret;
    }

    /**
     * Set ra2ClientPret
     *
     * @param string $ra2ClientPret
     *
     * @return Caution
     */
    public function setRa2ClientPret($ra2ClientPret)
    {
        $this->ra2ClientPret = $ra2ClientPret;
    
        return $this;
    }

    /**
     * Get ra2ClientPret
     *
     * @return string 
     */
    public function getRa2ClientPret()
    {
        return $this->ra2ClientPret;
    }

    /**
     * Set adresseCaution1
     *
     * @param string $adresseCaution1
     *
     * @return Caution
     */
    public function setAdresseCaution1($adresseCaution1)
    {
        $this->adresseCaution1 = $adresseCaution1;
    
        return $this;
    }

    /**
     * Get adresseCaution1
     *
     * @return string 
     */
    public function getAdresseCaution1()
    {
        return $this->adresseCaution1;
    }

    /**
     * Set adresseCaution2
     *
     * @param string $adresseCaution2
     *
     * @return Caution
     */
    public function setAdresseCaution2($adresseCaution2)
    {
        $this->adresseCaution2 = $adresseCaution2;
    
        return $this;
    }

    /**
     * Get adresseCaution2
     *
     * @return string 
     */
    public function getAdresseCaution2()
    {
        return $this->adresseCaution2;
    }

    /**
     * Set adresseCaution3
     *
     * @param string $adresseCaution3
     *
     * @return Caution
     */
    public function setAdresseCaution3($adresseCaution3)
    {
        $this->adresseCaution3 = $adresseCaution3;
    
        return $this;
    }

    /**
     * Get adresseCaution3
     *
     * @return string 
     */
    public function getAdresseCaution3()
    {
        return $this->adresseCaution3;
    }

    /**
     * Set cpCaution
     *
     * @param string $cpCaution
     *
     * @return Caution
     */
    public function setCpCaution($cpCaution)
    {
        $this->cpCaution = $cpCaution;
    
        return $this;
    }

    /**
     * Get cpCaution
     *
     * @return string 
     */
    public function getCpCaution()
    {
        return $this->cpCaution;
    }

    /**
     * Set villeCaution
     *
     * @param string $villeCaution
     *
     * @return Caution
     */
    public function setVilleCaution($villeCaution)
    {
        $this->villeCaution = $villeCaution;
    
        return $this;
    }

    /**
     * Get villeCaution
     *
     * @return string 
     */
    public function getVilleCaution()
    {
        return $this->villeCaution;
    }
    
    public function getAdresseCaution()
    {
        $i = 0;
        $adrFinale = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (trim($this->adresseCaution2) == '' && trim($this->adresseCaution3) == '') {
            $i++;
        }
        
        if (trim($this->adresseCaution1) != '') {
            $adrFinale[$i] = $this->adresseCaution1;
            $i++;
        }
        
        if (trim($this->adresseCaution2) != '') {
            $adrFinale[$i] = $this->adresseCaution2;
            $i++;
        }
        
        if (trim($this->adresseCaution3) != '') {
            $adrFinale[$i] = $this->adresseCaution3;
            $i++;
        }
        
        $adrFinale[$i] = $this->cpCaution . " " . $this->villeCaution;
        
        return $adrFinale;
    }

    /**
     * Set adressePret1
     *
     * @param string $adressePret1
     *
     * @return Caution
     */
    public function setAdressePret1($adressePret1)
    {
        $this->adressePret1 = $adressePret1;
    
        return $this;
    }

    /**
     * Get adressePret1
     *
     * @return string 
     */
    public function getAdressePret1()
    {
        return $this->adressePret1;
    }

    /**
     * Set adressePret2
     *
     * @param string $adressePret2
     *
     * @return Caution
     */
    public function setAdressePret2($adressePret2)
    {
        $this->adressePret2 = $adressePret2;
    
        return $this;
    }

    /**
     * Get adressePret2
     *
     * @return string 
     */
    public function getAdressePret2()
    {
        return $this->adressePret2;
    }

    /**
     * Set adressePret3
     *
     * @param string $adressePret3
     *
     * @return Caution
     */
    public function setAdressePret3($adressePret3)
    {
        $this->adressePret3 = $adressePret3;
    
        return $this;
    }

    /**
     * Get adressePret3
     *
     * @return string 
     */
    public function getAdressePret3()
    {
        return $this->adressePret3;
    }

    /**
     * Set cpPret
     *
     * @param string $cpPret
     *
     * @return Caution
     */
    public function setCpPret($cpPret)
    {
        $this->cpPret = $cpPret;
    
        return $this;
    }

    /**
     * Get cpPret
     *
     * @return string 
     */
    public function getCpPret()
    {
        return $this->cpPret;
    }

    /**
     * Set villePret
     *
     * @param string $villePret
     *
     * @return Caution
     */
    public function setVillePret($villePret)
    {
        $this->villePret = $villePret;
    
        return $this;
    }

    /**
     * Get villePret
     *
     * @return string 
     */
    public function getVillePret()
    {
        return $this->villePret;
    }
    
    public function getAdressePret()
    {
        $i = 0;
        $adrFinale = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (trim($this->adressePret2) == '' && trim($this->adressePret3) == '') {
            $i++;
        }
        
        if (trim($this->adressePret1) != '') {
            $adrFinale[$i] = $this->adressePret1;
            $i++;
        }
        
        if (trim($this->adressePret2) != '') {
            $adrFinale[$i] = $this->adressePret2;
            $i++;
        }
        
        if (trim($this->adressePret3) != '') {
            $adrFinale[$i] = $this->adressePret3;
            $i++;
        }
        
        $adrFinale[$i] = $this->cpPret . " " . $this->villePret;
        
        return $adrFinale;
    }

    /**
     * Set dateSituation
     *
     * @param string $dateSituation
     *
     * @return Caution
     */
    public function setDateSituation($dateSituation)
    {
        $this->dateSituation = $dateSituation;
    
        return $this;
    }

    /**
     * Get dateSituation
     *
     * @return string 
     */
    public function getDateSituation()
    {
        return $this->dateSituation;
    }

    /**
     * Set mtCautionInit
     *
     * @param float $mtCautionInit
     *
     * @return Caution
     */
    public function setMtCautionInit($mtCautionInit)
    {
        $this->mtCautionInit = $mtCautionInit;
    
        return $this;
    }

    /**
     * Get mtCautionInit
     *
     * @return float 
     */
    public function getMtCautionInit()
    {
        return number_format($this->mtCautionInit, 2, ",", " ");
    }

    /**
     * Set dateFin
     *
     * @param string $dateFin
     *
     * @return Caution
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    
        return $this;
    }

    /**
     * Get dateFin
     *
     * @return string 
     */
    public function getDateFin()
    {
        // ENG : AAAAMMJJ
        $annee = substr($this->dateFin, 0, 4);
        $mois = substr($this->dateFin, 4, 2);
        $jour = substr($this->dateFin, 6, 2);

        // nous JJ/MM/YYYY
        return $jour . "/" . $mois . "/" . $annee;
    }

    /**
     * Set idNumDossierPret
     *
     * @param string $idNumDossierPret
     *
     * @return Caution
     */
    public function setIdNumDossierPret($idNumDossierPret)
    {
        $this->idNumDossierPret = $idNumDossierPret;
    
        return $this;
    }

    /**
     * Get idNumDossierPret
     *
     * @return string 
     */
    public function getIdNumDossierPret()
    {
        return $this->idNumDossierPret;
    }

    /**
     * Set idDossier
     *
     * @param string $idDossier
     *
     * @return Caution
     */
    public function setIdDossier($idDossier)
    {
        $this->idDossier = $idDossier;
    
        return $this;
    }

    /**
     * Get idDossier
     *
     * @return string 
     */
    public function getIdDossier()
    {
        return trim(number_format($this->idDossier, 0));
    }

    /**
     * Set idPret
     *
     * @param string $idPret
     *
     * @return Caution
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
     * Set mtPretInit
     *
     * @param float $mtPretInit
     *
     * @return Caution
     */
    public function setMtPretInit($mtPretInit)
    {
        $this->mtPretInit = $mtPretInit;
    
        return $this;
    }

    /**
     * Get mtPretInit
     *
     * @return float 
     */
    public function getMtPretInit()
    {
        return number_format($this->mtPretInit, 2, ",", " ");
    }

    /**
     * Set taux
     *
     * @param float $taux
     *
     * @return Caution
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
        if ($this->taux) {
            return number_format($this->taux, 2, ",", " ");
        }
        
        return null;
    }

    /**
     * Set mtPretRestDu
     *
     * @param float $mtPretRestDu
     *
     * @return Caution
     */
    public function setMtPretRestDu($mtPretRestDu)
    {
        $this->mtPretRestDu = $mtPretRestDu;
    
        return $this;
    }

    /**
     * Get mtPretRestDu
     *
     * @return float 
     */
    public function getMtPretRestDu()
    {
        return number_format($this->mtPretRestDu, 2, ",", " ");
    }

    /**
     * Set mtInteret
     *
     * @param float $mtInteret
     *
     * @return Caution
     */
    public function setMtInteret($mtInteret)
    {
        $this->mtInteret = $mtInteret;
    
        return $this;
    }

    /**
     * Get mtInteret
     *
     * @return float 
     */
    public function getMtInteret()
    {
        return number_format($this->mtInteret, 2, ",", " ");
    }

    /**
     * Set libCodeTaux
     *
     * @param string $libCodeTaux
     *
     * @return Caution
     */
    public function setLibCodeTaux($libCodeTaux)
    {
        $this->libCodeTaux = $libCodeTaux;
    
        return $this;
    }

    /**
     * Get libCodeTaux
     *
     * @return string 
     */
    public function getLibCodeTaux()
    {
        return $this->libCodeTaux;
    }

    /**
     * Set valCodeTaux
     *
     * @param float $valCodeTaux
     *
     * @return Caution
     */
    public function setValCodeTaux($valCodeTaux)
    {
        $this->valCodeTaux = $valCodeTaux;
    
        return $this;
    }

    /**
     * Get valCodeTaux
     *
     * @return float 
     */
    public function getValCodeTaux()
    {
        return $this->valCodeTaux;
    }

    /**
     * Set margeTaux
     *
     * @param float $margeTaux
     *
     * @return Caution
     */
    public function setMargeTaux($margeTaux)
    {
        $this->margeTaux = $margeTaux;
    
        return $this;
    }

    /**
     * Get margeTaux
     *
     * @return float 
     */
    public function getMargeTaux()
    {
        return number_format($this->margeTaux, 2, ",", " ");
    }

    /**
     * Set mtEngagementInit
     *
     * @param float $mtEngagementInit
     *
     * @return Caution
     */
    public function setMtEngagementInit($mtEngagementInit)
    {
        $this->mtEngagementInit = $mtEngagementInit;
    
        return $this;
    }

    /**
     * Get mtEngagementInit
     *
     * @return float 
     */
    public function getMtEngagementInit()
    {
        return number_format($this->mtEngagementInit, 2, ",", " ");
    }

    /**
     * Set typeImpression
     *
     * @param float $typeImpression
     *
     * @return Caution
     */
    public function setTypeImpression($typeImpression)
    {
        $this->typeImpression = $typeImpression;

        return $this;
    }

    /**
     * Get typeImpression
     *
     * @return float
     */
    public function getTypeImpression()
    {
        if ($this->typeImpression) {
            return $this->typeImpression;
        }

        return 'ANGERS';
    }
}
