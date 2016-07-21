<?php

namespace Editique\CreditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Credit
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class SouscriptionEditique
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
     * \\\@ORM\Column(name="idDossier", type="string")
     */
    private $idDossier;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="idPret", type="string")
     */
    private $idPret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="idClient", type="string")
     */
    private $idClient;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="capital", type="string")
     */
    private $capital;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="rcs", type="string")
     */
    private $rcs;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="dirigeants", type="array")
     */
    private $dirigeants;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="objetFinancement", type="array")
     */
    private $objetFinancement;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="garanties", type="array")
     */
    private $garanties;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="assurance1", type="string", length=69)
     */
    private $assurance1;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="assurance2", type="string", length=69)
     */
    private $assurance2;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="assurance3", type="string", length=69)
     */
    private $assurance3;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="assurance4", type="string", length=69)
     */
    private $assurance4;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="assurance5", type="string", length=69)
     */
    private $assurance5;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="garantie1", type="boolean")
     */
    private $garantie1;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="garantie2", type="boolean")
     */
    private $garantie2;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="garantie3", type="boolean")
     */
    private $garantie3;
    
    /**
     * @var string
     *
     * \\\@ORM\Column(name="raisonSociale1", type="string")
     */
    private $raisonSociale1;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="raisonSociale2", type="string")
     */
    private $raisonSociale2;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="adresse1", type="string")
     */
    private $adresse1;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="adresse2", type="string")
     */
    private $adresse2;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="adresse3", type="string")
     */
    private $adresse3;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="codePostal", type="string")
     */
    private $codePostal;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="ville", type="string")
     */
    private $ville;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="formeJuridique", type="string")
     */
    private $formeJuridique;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="siren", type="string")
     */
    private $siren;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="montantPret", type="string")
     */
    private $montantPret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="duree", type="string")
     */
    private $duree;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="dtDecaissement", type="string")
     */
    private $dtDecaissement;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="dtDerEcheance", type="string")
     */
    private $dtDerEcheance;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="cptSupport", type="string")
     */
    private $cptSupport;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="nbEcheances", type="string")
     */
    private $nbEcheances;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="diffAmo", type="string")
     */
    private $diffAmo;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="modeAmo", type="string")
     */
    private $modeAmo;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="periodeInteret", type="string")
     */
    private $periodeInteret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="jourPrelevement", type="string")
     */
    private $jourPrelevement;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="tauxPret", type="string")
     */
    private $tauxPret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="margeTauxPret", type="string")
     */
    private $margeTauxPret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="codeTauxPret", type="string")
     */
    private $codeTauxPret;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="libCodeTaux", type="string")
     */
    private $libCodeTaux;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="typeCalcul", type="string")
     */
    private $typeCalcul;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="fraisDossier", type="string")
     */
    private $fraisDossier;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="teg", type="string")
     */
    private $teg;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="fraisGaranties", type="string")
     */
    private $fraisGaranties;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="identification", type="array")
     */
    private $identification;

    /**
     * @var string
     *
     * \\\@ORM\Column(name="nombre_exemplaire", type="string")
     */
    private $nombreExemplaire;

    /**
     * @var string
     *
     * ///@ORM\Column(name="description_ei", type="string")
     */
    private $descriptionEi;

    /**
     * @var string
     *
     * ///@ORM\Column(name="date_naissance", type="string")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * ///@ORM\Column(name="ville_naissance", type="string")
     */
    private $villeNaissance;

    /**
     * @var string
     *
     * ///@ORM\Column(name="localisation_naissance", type="string")
     */
    private $localisationNaissance;

    /**
     * @var string
     *
     * ///@ORM\Column(name="type_client", type="string")
     */
    private $typeClient;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idDossier
     *
     * @param string $idDossier
     *
     * @return Souscription
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
        return trim($this->idDossier);
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
        return trim($this->idPret);
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Souscription
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
        return trim($this->idClient);
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
        return trim($this->capital);
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
     * Set objetFinancement
     *
     * @param array $objetFinancement
     *
     * @return Souscription
     */
    public function setObjetFinancement($objetFinancement)
    {
        $this->objetFinancement = $objetFinancement;
    
        return $this;
    }

    /**
     * Get objetFinancement
     *
     * @return array
     */
    public function getObjetFinancement()
    {
        return $this->objetFinancement;
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

    /**
     * Set assurance1
     *
     * @param string $assurance1
     *
     * @return Souscription
     */
    public function setAssurance1($assurance1)
    {
        $this->assurance1 = $assurance1;
    
        return $this;
    }

    /**
     * Get assurance1
     *
     * @return string
     */
    public function getAssurance1()
    {
        return $this->assurance1;
    }

    /**
     * Set assurance2
     *
     * @param string $assurance2
     *
     * @return Souscription
     */
    public function setAssurance2($assurance2)
    {
        $this->assurance2 = $assurance2;
    
        return $this;
    }

    /**
     * Get assurance2
     *
     * @return string
     */
    public function getAssurance2()
    {
        return $this->assurance2;
    }

    /**
     * Set assurance3
     *
     * @param string $assurance3
     *
     * @return Souscription
     */
    public function setAssurance3($assurance3)
    {
        $this->assurance3 = $assurance3;
    
        return $this;
    }

    /**
     * Get assurance3
     *
     * @return string
     */
    public function getAssurance3()
    {
        return $this->assurance3;
    }

    /**
     * Set assurance4
     *
     * @param string $assurance4
     *
     * @return Souscription
     */
    public function setAssurance4($assurance4)
    {
        $this->assurance4 = $assurance4;
    
        return $this;
    }

    /**
     * Get assurance4
     *
     * @return string
     */
    public function getAssurance4()
    {
        return $this->assurance4;
    }

    /**
     * Set assurance5
     *
     * @param string $assurance5
     *
     * @return Souscription
     */
    public function setAssurance5($assurance5)
    {
        $this->assurance5 = $assurance5;
    
        return $this;
    }

    /**
     * Get assurance5
     *
     * @return string
     */
    public function getAssurance5()
    {
        return $this->assurance5;
    }
    
    public function getAssurances()
    {
        $arr = array(
            $this->assurance1,
            $this->assurance2,
            $this->assurance3,
            $this->assurance4,
            $this->assurance5
        );
        
        return $arr;
    }
    
    /**
     * Set garantie1
     *
     * @param string $garantie1
     *
     * @return Souscription
     */
    public function setGarantie1($garantie1)
    {
        $this->garantie1 = $garantie1;
    
        return $this;
    }

    /**
     * Get garantie1
     *
     * @return string
     */
    public function getGarantie1()
    {
        return $this->garantie1;
    }

    /**
     * Set garantie2
     *
     * @param string $garantie2
     *
     * @return Souscription
     */
    public function setGarantie2($garantie2)
    {
        $this->garantie2 = $garantie2;
    
        return $this;
    }

    /**
     * Get garantie2
     *
     * @return string
     */
    public function getGarantie2()
    {
        return $this->garantie2;
    }

    /**
     * Set garantie3
     *
     * @param string $garantie3
     *
     * @return Souscription
     */
    public function setGarantie3($garantie3)
    {
        $this->garantie3 = $garantie3;
    
        return $this;
    }

    /**
     * Get garantie3
     *
     * @return string
     */
    public function getGarantie3()
    {
        return $this->garantie3;
    }

    /**
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     *
     * @return Souscription
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
        return trim($this->raisonSociale1);
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     *
     * @return Souscription
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
        return trim($this->raisonSociale2);
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return Souscription
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;
    
        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return trim($this->adresse1);
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return Souscription
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;
    
        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return trim($this->adresse2);
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     *
     * @return Souscription
     */
    public function setAdresse3($adresse3)
    {
        $this->adresse3 = $adresse3;
    
        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string
     */
    public function getAdresse3()
    {
        return trim($this->adresse3);
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Souscription
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    
        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return trim($this->codePostal);
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Souscription
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    
        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return trim($this->ville);
    }

    /**
     * Set formeJuridique
     *
     * @param string $formeJuridique
     *
     * @return Souscription
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
        return trim($this->formeJuridique);
    }

    /**
     * Set siren
     *
     * @param string $siren
     *
     * @return Souscription
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
        return trim($this->siren);
    }

    /**
     * Set montantPret
     *
     * @param string $montantPret
     *
     * @return Souscription
     */
    public function setMontantPret($montantPret)
    {
        $this->montantPret = $montantPret;
    
        return $this;
    }

    /**
     * Get montantPret
     *
     * @return string
     */
    public function getMontantPret()
    {
        $numLetter = \BackOffice\ParserBundle\Manager\EcritureManager::asLetters((float) $this->montantPret);
        if (substr($numLetter, -8) != 'centimes') {
            $numLetter = $numLetter . ' euros';
        }
        
        $montant = trim(number_format((float) $this->montantPret, 2, ',', ' ')) . ' euros (' . $numLetter . ')';
        
        $montantFormate = wordwrap($montant, 69, "#&");
        $array = explode("#&", $montantFormate);
        
        if (count($array) == 1) {
            $array[1] = null;
        }
        
        return $array;
    }

    /**
     * Set duree
     *
     * @param string $duree
     *
     * @return Souscription
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
    
        return $this;
    }

    /**
     * Get duree
     *
     * @return string
     */
    public function getDuree()
    {
        return $this->duree . " mois";
    }

    /**
     * Set dtDecaissement
     *
     * @param string $dtDecaissement
     *
     * @return Souscription
     */
    public function setDtDecaissement($dtDecaissement)
    {
        $this->dtDecaissement = $dtDecaissement;
    
        return $this;
    }

    /**
     * Get dtDecaissement
     *
     * @return string
     */
    public function getDtDecaissement()
    {
        return trim($this->dtDecaissement);
    }

    /**
     * Set dtDerEcheance
     *
     * @param string $dtDerEcheance
     *
     * @return Souscription
     */
    public function setDtDerEcheance($dtDerEcheance)
    {
        $this->dtDerEcheance = $dtDerEcheance;
    
        return $this;
    }

    /**
     * Get dtDerEcheance
     *
     * @return string
     */
    public function getDtDerEcheance()
    {
        return trim($this->dtDerEcheance);
    }

    /**
     * Set cptSupport
     *
     * @param string $cptSupport
     *
     * @return Souscription
     */
    public function setCptSupport($cptSupport)
    {
        $this->cptSupport = $cptSupport;
    
        return $this;
    }

    /**
     * Get cptSupport
     *
     * @return string
     */
    public function getCptSupport()
    {
        return trim($this->cptSupport);
    }

    /**
     * Set nbEcheances
     *
     * @param string $nbEcheances
     *
     * @return Souscription
     */
    public function setNbEcheances($nbEcheances)
    {
        $this->nbEcheances = $nbEcheances;
    
        return $this;
    }

    /**
     * Get nbEcheances
     *
     * @return string
     */
    public function getNbEcheances()
    {
        if (trim($this->nbEcheances) <= 1) {
            return trim($this->nbEcheances) . " échéance";
        } else {
            return trim($this->nbEcheances) . " échéances";
        }
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
        return trim($this->diffAmo);
    }

    /**
     * Set modeAmo
     *
     * @param string $modeAmo
     *
     * @return Souscription
     */
    public function setModeAmo($modeAmo)
    {
        $this->modeAmo = $modeAmo;
    
        return $this;
    }

    /**
     * Get modeAmo
     *
     * @return string
     */
    public function getModeAmo()
    {
        return trim($this->modeAmo);
    }

    /**
     * Set periodeInteret
     *
     * @param string $periodeInteret
     *
     * @return Souscription
     */
    public function setPeriodeInteret($periodeInteret)
    {
        $this->periodeInteret = $periodeInteret;
    
        return $this;
    }

    /**
     * Get periodeInteret
     *
     * @return string
     */
    public function getPeriodeInteret()
    {
        return trim($this->periodeInteret);
    }

    /**
     * Set jourPrelevement
     *
     * @param string $jourPrelevement
     *
     * @return Souscription
     */
    public function setJourPrelevement($jourPrelevement)
    {
        $this->jourPrelevement = $jourPrelevement;
    
        return $this;
    }

    /**
     * Get jourPrelevement
     *
     * @return string
     */
    public function getJourPrelevement()
    {
        return trim($this->jourPrelevement);
    }

    /**
     * Set tauxPret
     *
     * @param string $tauxPret
     *
     * @return Souscription
     */
    public function setTauxPret($tauxPret)
    {
        $this->tauxPret = $tauxPret;
    
        return $this;
    }

    /**
     * Get tauxPret
     *
     * @return string
     */
    public function getTauxPret()
    {
        return trim(number_format($this->tauxPret, 2, ',', ' ')) . " %";
    }

    /**
     * Set margeTauxPret
     *
     * @param string $margeTauxPret
     *
     * @return Souscription
     */
    public function setMargeTauxPret($margeTauxPret)
    {
        $this->margeTauxPret = $margeTauxPret;
    
        return $this;
    }

    /**
     * Get margeTauxPret
     *
     * @return string
     */
    public function getMargeTauxPret()
    {
        return trim($this->margeTauxPret);
    }

    /**
     * Set codeTauxPret
     *
     * @param string $codeTauxPret
     *
     * @return Souscription
     */
    public function setCodeTauxPret($codeTauxPret)
    {
        $this->codeTauxPret = $codeTauxPret;
    
        return $this;
    }

    /**
     * Get codeTauxPret
     *
     * @return string
     */
    public function getCodeTauxPret()
    {
        return trim($this->codeTauxPret);
    }

    /**
     * Set libCodeTaux
     *
     * @param string $libCodeTaux
     *
     * @return Souscription
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
        return trim($this->libCodeTaux);
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
        return trim($this->typeCalcul);
    }

    /**
     * Set fraisDossier
     *
     * @param string $fraisDossier
     *
     * @return Souscription
     */
    public function setFraisDossier($fraisDossier)
    {
        $this->fraisDossier = $fraisDossier;
    
        return $this;
    }

    /**
     * Get fraisDossier
     *
     * @return string
     */
    public function getFraisDossier()
    {
        if (trim($this->fraisDossier)) {
            return trim($this->fraisDossier) . " euros";
        }
    }

    /**
     * Set teg
     *
     * @param string $teg
     *
     * @return Souscription
     */
    public function setTeg($teg)
    {
        $this->teg = $teg;
    
        return $this;
    }

    /**
     * Get teg
     *
     * @return string
     */
    public function getTeg()
    {
        return trim(number_format($this->teg, 2, ',', ' ')) . " %";
    }

    /**
     * Set fraisGaranties
     *
     * @param string $fraisGaranties
     *
     * @return Souscription
     */
    public function setFraisGaranties($fraisGaranties)
    {
        $this->fraisGaranties = $fraisGaranties;
    
        return $this;
    }

    /**
     * Get fraisGaranties
     *
     * @return string
     */
    public function getFraisGaranties()
    {
        if ($this->fraisGaranties) {
            return $this->fraisGaranties;
        }
    }
    
    /**
     * Set identification
     *
     * @param array identification
     *
     * @return Souscription
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;
    
        return $this;
    }

    /**
     * Get identification
     *
     * @return array
     */
    public function getIdentification()
    {
        return $this->identification;
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
     * Set dateNaissance
     *
     * @param string $dateNaissance
     *
     * @return SouscriptionEditique
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    
        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return string
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set villeNaissance
     *
     * @param string $villeNaissance
     *
     * @return SouscriptionEditique
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
     * Set localisationNaissance
     *
     * @param string $localisationNaissance
     *
     * @return SouscriptionEditique
     */
    public function setLocalisationNaissance($localisationNaissance)
    {
        $this->localisationNaissance = $localisationNaissance;
    
        return $this;
    }

    /**
     * Get localisationNaissance
     *
     * @return string
     */
    public function getLocalisationNaissance()
    {
        return $this->localisationNaissance;
    }

    /**
     * Set typeClient
     *
     * @param string $typeClient
     *
     * @return SouscriptionEditique
     */
    public function setTypeClient($typeClient)
    {
        $this->typeClient = $typeClient;
    
        return $this;
    }

    /**
     * Get typeClient
     *
     * @return string
     */
    public function getTypeClient()
    {
        return $this->typeClient;
    }

    /**
     * Set descriptionEi
     *
     * @param string $descriptionEi
     *
     * @return SouscriptionEditique
     */
    public function setDescriptionEi($descriptionEi)
    {
        $this->descriptionEi = $descriptionEi;
    
        return $this;
    }

    /**
     * Get descriptionEi
     *
     * @return string
     */
    public function getDescriptionEi()
    {
        return $this->descriptionEi;
    }

    /**
     * Set comeng1
     *
     * @param string $comeng1
     *
     * @return SouscriptionEditique
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
     * @return SouscriptionEditique
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
}
