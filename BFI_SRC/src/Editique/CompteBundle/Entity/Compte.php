<?php

namespace Editique\CompteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Compte
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
     * @ORM\Column(name="codEve", type="string", length=6)
     */
    private $codEve;

    /**
     * @var string
     *
     * @ORM\Column(name="codEdi", type="string", length=6)
     */
    private $codEdi;

    /**
     * @var string
     *
     * @ORM\Column(name="codEta", type="string", length=6)
     */
    private $codEta;

    /**
     * @var string
     *
     * @ORM\Column(name="codProSou", type="string", length=6)
     */
    private $codProSou;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=46)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="raisonSociale1", type="string", length=32)
     */
    private $raisonSociale1;

    /**
     * @var string
     *
     * @ORM\Column(name="raisonSociale2", type="string", length=32)
     */
    private $raisonSociale2;

    /**
     * @var string
     *
     * @ORM\Column(name="formeJuridique", type="string", length=4)
     */
    private $formeJuridique;

    /**
     * @var string
     *
     * @ORM\Column(name="siren", type="string", length=9)
     */
    private $siren;

    /**
     * @var string
     *
     * @ORM\Column(name="adrSiege1", type="string", length=32)
     */
    private $adrSiege1;

    /**
     * @var string
     *
     * @ORM\Column(name="adrSiege2", type="string", length=32)
     */
    private $adrSiege2;

    /**
     * @var string
     *
     * @ORM\Column(name="adrSiege3", type="string", length=32)
     */
    private $adrSiege3;

    /**
     * @var string
     *
     * @ORM\Column(name="adrSiegeCP", type="string", length=6)
     */
    private $adrSiegeCP;

    /**
     * @var string
     *
     * @ORM\Column(name="adrSiegeVil", type="string", length=25)
     */
    private $adrSiegeVil;

    /**
     * @var string
     *
     * @ORM\Column(name="adrCourrier1", type="string", length=32)
     */
    private $adrCourrier1;

    /**
     * @var string
     *
     * @ORM\Column(name="adrCourrier2", type="string", length=32)
     */
    private $adrCourrier2;

    /**
     * @var string
     *
     * @ORM\Column(name="adrCourrier3", type="string", length=32)
     */
    private $adrCourrier3;

    /**
     * @var string
     *
     * @ORM\Column(name="adrCourrierCP", type="string", length=6)
     */
    private $adrCourrierCP;

    /**
     * @var string
     *
     * @ORM\Column(name="adrCourrierVil", type="string", length=25)
     */
    private $adrCourrierVil;

    /**
     * @var string
     *
     * @ORM\Column(name="telSociete", type="string", length=50)
     */
    private $telSociete;
    
    /**
     * @var string
     *
     * @ORM\Column(name="faxSociete", type="string", length=50)
     */
    private $faxSociete;

    /**
     * @var string
     *
     * @ORM\Column(name="emailSociete", type="string", length=50)
     */
    private $emailSociete;

    /**
     * @var string
     *
     * @ORM\Column(name="idClient", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="numCompte", type="string", length=20)
     */
    private $numCompte;

    /**
     * @var string
     *
     * @ORM\Column(name="catTiers", type="string", length=3)
     */
    private $catTiers;

    /**
     * @var string
     *
     * @ORM\Column(name="tauxCompte", type="string", length=22)
     */
    private $tauxCompte;
    
    /**
     * @var string
     *
     * @ORM\Column(name="idEsab", type="string", length=12)
     */
    private $idEsab;

    /**
     * @var string
     *
     * @ORM\Column(name="mdpEsab", type="string", length=64)
     */
    private $mdpEsab;
    
    
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
     * Set codEve
     *
     * @param string $codEve
     *
     * @return Compte
     */
    public function setCodEve($codEve)
    {
        $this->codEve = $codEve;
    
        return $this;
    }

    /**
     * Get codEve
     *
     * @return string
     */
    public function getCodEve()
    {
        return $this->codEve;
    }

    /**
     * Set codEdi
     *
     * @param string $codEdi
     *
     * @return Compte
     */
    public function setCodEdi($codEdi)
    {
        $this->codEdi = $codEdi;
    
        return $this;
    }

    /**
     * Get codEdi
     *
     * @return string
     */
    public function getCodEdi()
    {
        return $this->codEdi;
    }

    /**
     * Set codEta
     *
     * @param string $codEta
     *
     * @return Compte
     */
    public function setCodEta($codEta)
    {
        $this->codEta = $codEta;
    
        return $this;
    }

    /**
     * Get codEta
     *
     * @return string
     */
    public function getCodEta()
    {
        return $this->codEta;
    }

    /**
     * Set codProSou
     *
     * @param string $codProSou
     *
     * @return Compte
     */
    public function setCodProSou($codProSou)
    {
        $this->codProSou = $codProSou;
    
        return $this;
    }

    /**
     * Get codProSou
     *
     * @return string
     */
    public function getCodProSou()
    {
        return $this->codProSou;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Compte
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     *
     * @return Compte
     */
    public function setRaisonSociale1($raisonSociale1)
    {
        $this->raisonSociale1 = $raisonSociale1;
    
        return $this;
    }

    /**
     * Get raisonSociale1
     *
     * @return string
     */
    public function getRaisonSociale1()
    {
        return $this->raisonSociale1;
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     *
     * @return Compte
     */
    public function setRaisonSociale2($raisonSociale2)
    {
        $this->raisonSociale2 = $raisonSociale2;
    
        return $this;
    }

    /**
     * Get raisonSociale2
     *
     * @return string
     */
    public function getRaisonSociale2()
    {
        return $this->raisonSociale2;
    }
    
    public function getRaisonSociale()
    {
        $rs = null;
        if (!is_array($this->raisonSociale1)) {
            $rs .= $this->raisonSociale1;
        }
        
        if (!is_array($this->raisonSociale2)) {
            $rs .= " " . $this->raisonSociale2;
        }
        
        return trim($rs);
    }

    /**
     * Set formeJuridique
     *
     * @param string $formeJuridique
     *
     * @return Compte
     */
    public function setFormeJuridique($formeJuridique)
    {
        $this->formeJuridique = $formeJuridique;
    
        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return string
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }

    /**
     * Set siren
     *
     * @param string $siren
     *
     * @return Compte
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;
    
        return $this;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set adrSiege1
     *
     * @param string $adrSiege1
     *
     * @return Compte
     */
    public function setAdrSiege1($adrSiege1)
    {
        $this->adrSiege1 = $adrSiege1;
    
        return $this;
    }

    /**
     * Get adrSiege1
     *
     * @return string
     */
    public function getAdrSiege1()
    {
        if (!is_array($this->adrSiege1)) {
            return $this->adrSiege1;
        }
        
        return null;
    }

    /**
     * Set adrSiege2
     *
     * @param string $adrSiege2
     *
     * @return Compte
     */
    public function setAdrSiege2($adrSiege2)
    {
        $this->adrSiege2 = $adrSiege2;
    
        return $this;
    }

    /**
     * Get adrSiege2
     *
     * @return string
     */
    public function getAdrSiege2()
    {
        if (!is_array($this->adrSiege2)) {
            return $this->adrSiege2;
        }
        
        return null;
    }

    /**
     * Set adrSiege3
     *
     * @param string $adrSiege3
     *
     * @return Compte
     */
    public function setAdrSiege3($adrSiege3)
    {
        $this->adrSiege3 = $adrSiege3;
    
        return $this;
    }

    /**
     * Get adrSiege3
     *
     * @return string
     */
    public function getAdrSiege3()
    {
        if (!is_array($this->adrSiege3)) {
            return $this->adrSiege3;
        }
        
        return null;
    }

    /**
     * Set adrSiegeCP
     *
     * @param string $adrSiegeCP
     *
     * @return Compte
     */
    public function setAdrSiegeCP($adrSiegeCP)
    {
        $this->adrSiegeCP = $adrSiegeCP;
    
        return $this;
    }

    /**
     * Get adrSiegeCP
     *
     * @return string
     */
    public function getAdrSiegeCP()
    {
        if (!is_array($this->adrSiegeCP)) {
            return $this->adrSiegeCP;
        }
        
        return null;
    }

    /**
     * Set adrSiegeVil
     *
     * @param string $adrSiegeVil
     *
     * @return Compte
     */
    public function setAdrSiegeVil($adrSiegeVil)
    {
        $this->adrSiegeVil = $adrSiegeVil;
    
        return $this;
    }

    /**
     * Get adrSiegeVil
     *
     * @return string
     */
    public function getAdrSiegeVil()
    {
        if (!is_array($this->adrSiegeVil)) {
            return $this->adrSiegeVil;
        }
        
        return null;
    }
    
    public function getAdresseSiege()
    {
        $i = 0;
        $adrSiege = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (is_array($this->adrSiege2) && is_array($this->adrSiege3)) {
            $i++;
        }
        
        if (!is_array($this->adrSiege1)) {
            $adrSiege[$i] = $this->adrSiege1;
            $i++;
        }
        
        if (!is_array($this->adrSiege2)) {
            $adrSiege[$i] = $this->adrSiege2;
            $i++;
        }
        
        if (!is_array($this->adrSiege3)) {
            $adrSiege[$i] = $this->adrSiege3;
            $i++;
        }
        
        $adrSiege[$i] = $this->adrSiegeCP . " " . $this->adrSiegeVil;
        
        return $adrSiege;
    }
    
    /**
     * Set adrCourrier1
     *
     * @param string $adrCourrier1
     *
     * @return Compte
     */
    public function setAdrCourrier1($adrCourrier1)
    {
        $this->adrCourrier1 = $adrCourrier1;
    
        return $this;
    }

    /**
     * Get adrCourrier1
     *
     * @return string
     */
    public function getAdrCourrier1()
    {
        return $this->adrCourrier1;
    }

    /**
     * Set adrCourrier2
     *
     * @param string $adrCourrier2
     *
     * @return Compte
     */
    public function setAdrCourrier2($adrCourrier2)
    {
        $this->adrCourrier2 = $adrCourrier2;
    
        return $this;
    }

    /**
     * Get adrCourrier2
     *
     * @return string
     */
    public function getAdrCourrier2()
    {
        return $this->adrCourrier2;
    }

    /**
     * Set adrCourrier3
     *
     * @param string $adrCourrier3
     *
     * @return Compte
     */
    public function setAdrCourrier3($adrCourrier3)
    {
        $this->adrCourrier3 = $adrCourrier3;
    
        return $this;
    }

    /**
     * Get adrCourrier3
     *
     * @return string
     */
    public function getAdrCourrier3()
    {
        return $this->adrCourrier3;
    }

    /**
     * Set adrCourrierCP
     *
     * @param string $adrCourrierCP
     *
     * @return Compte
     */
    public function setAdrCourrierCP($adrCourrierCP)
    {
        $this->adrCourrierCP = $adrCourrierCP;
    
        return $this;
    }

    /**
     * Get adrCourrierCP
     *
     * @return string
     */
    public function getAdrCourrierCP()
    {
        return $this->adrCourrierCP;
    }

    /**
     * Set adrCourrierVil
     *
     * @param string $adrCourrierVil
     *
     * @return Compte
     */
    public function setAdrCourrierVil($adrCourrierVil)
    {
        $this->adrCourrierVil = $adrCourrierVil;
    
        return $this;
    }

    /**
     * Get adrCourrierVil
     *
     * @return string
     */
    public function getAdrCourrierVil()
    {
        return $this->adrCourrierVil;
    }
    
    public function getAdresseCourrier()
    {
        $i = 0;
        $adrCourrier = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (trim($this->adrCourrier2) == '' && trim($this->adrCourrier3) == '') {
            $i++;
        }
        
        if (trim($this->adrCourrier1) != '') {
            $adrCourrier[$i] = $this->adrCourrier1;
            $i++;
        }
        
        if (trim($this->adrCourrier2) != '') {
            $adrCourrier[$i] = $this->adrCourrier2;
            $i++;
        }
        
        if (trim($this->adrCourrier3) != '') {
            $adrCourrier[$i] = $this->adrCourrier3;
            $i++;
        }
        
        $adrCourrier[$i] = $this->adrCourrierCP . " " . $this->adrCourrierVil;
        
        return $adrCourrier;
    }
    
    public function getAdresseLettre()
    {
        // Si on a une adresse courrier, on la renvoie sinon on renvoi adresse siege
        if (trim($this->adrCourrierCP) != '' &&  trim($this->adrCourrierVil) != '') {
            return $this->getAdresseCourrier();
        } else {
            return $this->getAdresseSiege();
        }
    }

    /**
     * Set telSociete
     *
     * @param string $telSociete
     *
     * @return Compte
     */
    public function setTelSociete($telSociete)
    {
        $this->telSociete = $telSociete;
    
        return $this;
    }

    /**
     * Get telSociete
     *
     * @return string
     */
    public function getTelSociete()
    {
        if (!is_array($this->telSociete)) {
            return trim($this->telSociete);
        }
    }

    /**
     * Set faxSociete
     *
     * @param string $faxSociete
     *
     * @return Compte
     */
    public function setFaxSociete($faxSociete)
    {
        $this->faxSociete = $faxSociete;
    
        return $this;
    }

    /**
     * Get faxSociete
     *
     * @return string
     */
    public function getFaxSociete()
    {
        return trim($this->faxSociete);
    }

    /**
     * Set emailSociete
     *
     * @param string $emailSociete
     *
     * @return Compte
     */
    public function setEmailSociete($emailSociete)
    {
        $this->emailSociete = $emailSociete;
    
        return $this;
    }

    /**
     * Get emailSociete
     *
     * @return string
     */
    public function getEmailSociete()
    {
        if (!is_array($this->emailSociete)) {
            return $this->emailSociete;
        }
        
        return null;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Compte
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;
    
        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set numCompte
     *
     * @param string $numCompte
     *
     * @return Compte
     */
    public function setNumCompte($numCompte)
    {
        $this->numCompte = $numCompte;
    
        return $this;
    }

    /**
     * Get numCompte
     *
     * @return string
     */
    public function getNumCompte()
    {
        return $this->numCompte;
    }

    /**
     * Set catTiers
     *
     * @param string $catTiers
     *
     * @return Compte
     */
    public function setCatTiers($catTiers)
    {
        $this->catTiers = $catTiers;
    
        return $this;
    }

    /**
     * Get catTiers
     *
     * @return string
     */
    public function getCatTiers()
    {
        return $this->catTiers;
    }

    /**
     * Set tauxCompte
     *
     * @param string $tauxCompte
     *
     * @return Compte
     */
    public function setTauxCompte($tauxCompte)
    {
        $this->tauxCompte = $tauxCompte;
    
        return $this;
    }

    /**
     * Get tauxCompte
     *
     * @return string
     */
    public function getTauxCompte()
    {
        return $this->tauxCompte;
    }

    /**
     * Set idEsab
     *
     * @param string $idEsab
     *
     * @return Compte
     */
    public function setIdEsab($idEsab)
    {
        $this->idEsab = $idEsab;
    
        return $this;
    }

    /**
     * Get idEsab
     *
     * @return string
     */
    public function getIdEsab()
    {
        return $this->idEsab;
    }

    /**
     * Set mdpEsab
     *
     * @param string $mdpEsab
     *
     * @return Compte
     */
    public function setMdpEsab($mdpEsab)
    {
        $this->mdpEsab = $mdpEsab;
    
        return $this;
    }

    /**
     * Get mdpEsab
     *
     * @return string
     */
    public function getMdpEsab()
    {
        return $this->mdpEsab;
    }
}
