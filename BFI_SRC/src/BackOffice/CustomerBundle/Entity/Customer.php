<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mageekguy\atoum\asserters\string;
use mageekguy\atoum\tests\units\asserters\boolean;
use mageekguy\atoum\tests\units\asserters\integer;

/**
 * Customer
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="siret",columns={"siren","codenic"})})
 * @ORM\Entity
 */
class Customer
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
     * @ORM\Column(name="idcustomer", type="string",unique=true)
     */
    private $idCustomer;

    /**
     * @var string
     *
     * @ORM\Column(name="idsab", type="string",nullable = true,unique = true)
     */
    private $idSAB;

    /**
     * @ORM\ManyToOne(targetEntity="SettingsCivility", inversedBy="customers")
     * @ORM\JoinColumn(name="civility_id", referencedColumnName="id",nullable=true)
     */
    private $codeCivilite;


    /**
     * Set codeCivilite
     *
     * @param SettingsCivility $codeCivilite
     *
     * @return Customer
     */
    public function setcodeCivilite($codeCivilite)
    {
        $this->codeCivilite = $codeCivilite;

        return $this;
    }

    /**
     * Get codeCivilite
     *
     * @return SettingsCivility
     */
    public function getcodeCivilite()
    {
        return $this->codeCivilite;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="civility_mod", type="boolean", nullable = true)
     */
    private $civility_mod;

    /**
     * Set civility_mod
     *
     * @param bool $civility_mod
     *
     * @return Customer
     */
    public function setcivility_mod($civility_mod)
    {
        $this->civility_mod = $civility_mod;

        return $this;
    }

    /**
     * Get civility_mod
     *
     * @return bool
     */
    public function getcivility_mod()
    {
        return $this->civility_mod;
    }

    /**
     * @ORM\ManyToOne(targetEntity="SettingsStateCode", inversedBy="customers")
     * @ORM\JoinColumn(name="statecode_id", referencedColumnName="id",nullable=true)
     */
    private $codeEtat;

    /**
     * @ORM\ManyToOne(targetEntity="SettingsJuridique", inversedBy="customers")
     * @ORM\JoinColumn(name="juridique_id", referencedColumnName="id",nullable=true)
     */
    private $juridique;

    /**
     * Set codeEtat
     *
     * @param SettingsStateCode $codeEtat
     *
     * @return Customer
     */
    public function setcodeEtat($codeEtat)
    {
        $this->codeEtat = $codeEtat;
        return $this;
    }

    public function setJuridique(SettingsJuridique $juridique)
    {
        $this->juridique = $juridique;
        return $this;
    }

    /**
     * Get codeEtat
     *
     * @return SettingsStateCode
     */
    public function getcodeEtat()
    {
        return $this->codeEtat;
    }

    public function getJuridique()
    {
        return $this->juridique;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="etat_mod", type="boolean", nullable = true)
     */
    private $etat_mod;

    /**
     * Set etat_mod
     *
     * @param bool $etat_mod
     *
     * @return Customer
     */
    public function setetat_mod($etat_mod)
    {
        $this->etat_mod = $etat_mod;

        return $this;
    }

    /**
     * Get etat_mod
     *
     * @return bool
     */
    public function getetat_mod()
    {
        return $this->etat_mod;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="juridique_mod", type="boolean", nullable = true)
     */
    private $juridique_mod;

    /**
     * Set juridique_mod
     *
     * @param bool $juridique_mod
     *
     * @return Customer
     */
    public function setjuridique_mod($juridique_mod)
    {
        $this->juridique_mod = $juridique_mod;

        return $this;
    }

    /**
     * Get juridique_mod
     *
     * @return bool
     */
    public function getjuridique_mod()
    {
        return $this->juridique_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable = true)
     */
    private $designation;

    /**
     * Set designation
     *
     * @param integer $designation
     *
     * @return Customer
     */
    public function setdesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getdesignation()
    {
        return $this->designation;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="designation_mod", type="boolean", nullable = true)
     */
    private $designation_mod;

    /**
     * Set designation_mod
     *
     * @param bool $designation_mod
     *
     * @return Customer
     */
    public function setdesignation_mod($designation_mod)
    {
        $this->designation_mod = $designation_mod;

        return $this;
    }

    /**
     * Get designation_mod
     *
     * @return bool
     */
    public function getdesignation_mod()
    {
        return $this->designation_mod;
    }


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datenaissance", type="datetime", nullable = true)
     */
    private $dateNaissance;

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Customer
     */
    public function setdateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get codeClient
     *
     * @return \DateTime
     */
    public function getdateNaissance()
    {
        return $this->dateNaissance;
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="siren", type="string",length=9, nullable = true,unique = false)
     */
    private $siren;


    /**
     * Set siren
     *
     * @param string $siren
     *
     * @return Customer
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return integer
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="siren_mod", type="boolean", nullable = true)
     */
    private $siren_mod;

    /**
     * Set siren_mod
     *
     * @param bool $siren_mod
     *
     * @return Customer
     */
    public function setsiren_mod($siren_mod)
    {
        $this->siren_mod = $siren_mod;

        return $this;
    }

    /**
     * Get siren_mod
     *
     * @return bool
     */
    public function getsiren_mod()
    {
        return $this->siren_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="codenic", type="string",length=5, nullable = true)
     */
    private $codeNic;

    /**
     * Set codeNic
     *
     * @param string $codeNic
     *
     * @return Customer
     */
    public function setcodeNic($codeNic)
    {
        $this->codeNic = $codeNic;

        return $this;
    }

    /**
     * Get codeNic
     *
     * @return string
     */
    public function getcodeNic()
    {
        return $this->codeNic;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="codenic_mod", type="boolean", nullable = true)
     */
    private $codenic_mod;

    /**
     * Set codenic_mod
     *
     * @param bool $codenic_mod
     *
     * @return Customer
     */
    public function setcodenic_mod($codenic_mod)
    {
        $this->codenic_mod = $codenic_mod;

        return $this;
    }

    /**
     * Get codenic_mod
     *
     * @return bool
     */
    public function getcodenic_mod()
    {
        return $this->codenic_mod;
    }

    /**
     * @var integer
     * @ORM\Column(name="capital",type="integer",nullable=true)
     */
    private $capital;

    /**
     * Set capital
     *
     * @param integer $capital
     *
     * @return Customer
     */
    public function setcapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get siren
     *
     * @return integer
     */
    public function getcapital()
    {
        return $this->capital;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="capital_mod", type="boolean", nullable = true)
     */
    private $capital_mod;

    /**
     * Set capital_mod
     *
     * @param bool $capital_mod
     *
     * @return Customer
     */
    public function setcapital_mod($capital_mod)
    {
        $this->capital_mod = $capital_mod;

        return $this;
    }

    /**
     * Get capital_mod
     *
     * @return bool
     */
    public function getcapital_mod()
    {
        return $this->capital_mod;
    }
    /**
     * @ORM\ManyToOne(targetEntity="SettingsResp", inversedBy="customers")
     * @ORM\JoinColumn(name="responsable_id", referencedColumnName="id",nullable=true)
     */
    private $responsable;

    /**
     * Set responsable
     *
     * @param SettingsResp $responsable
     *
     * @return Customer
     */
    public function setresponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getresponsable()
    {
        return $this->responsable;
    }



    /**
     * @ORM\ManyToOne(targetEntity="SettingsCategorie", inversedBy="customers")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id",nullable=true)
     */
    private $categorieClient;

    /**
     * Set categorieClient
     *
     * @param SettingsCategorie $categorieClient
     *
     * @return Customer
     */
    public function setcategorieClient($categorieClient)
    {
        $this->categorieClient = $categorieClient;
        //$categorieClient->addCustomer($this);

        return $this;
    }

    /**
     * Get categorieClient
     *
     * @return SettingsCategorie
     */
    public function getcategorieClient()
    {
        return $this->categorieClient;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="chiffreAffaire", type="string", length=255, nullable = true)
     */
    private $chiffreAffaire;

    /**
     * Set chiffreAffaire
     *
     * @param integer $chiffreAffaire
     *
     * @return Customer
     */
    public function setchiffreAffaire($chiffreAffaire)
    {
        $this->chiffreAffaire = $chiffreAffaire;

        return $this;
    }

    /**
     * Get chiffreAffaire
     *
     * @return integer
     */
    public function getchiffreAffaire()
    {
        return $this->chiffreAffaire;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="codePays", type="string", nullable = true)
     */
    private $codePays;

    /**
     * Set codePays
     *
     * @param integer $codePays
     *
     * @return Customer
     */
    public function setcodePays($codePays)
    {
        $this->codePays = $codePays;

        return $this;
    }

    /**
     * Get codePays
     *
     * @return integer
     */
    public function getcodePays()
    {
        return $this->codePays;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable = true)
     */
    private $adresse;

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Customer
     */
    public function setadresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getadresse()
    {
        return $this->adresse;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="adresse_mod", type="boolean", nullable = true)
     */
    private $adresse_mod;

    /**
     * Set email_mod
     *
     * @param bool $adresse_mod
     *
     * @return Customer
     */
    public function setadresse_mod($adresse_mod)
    {
        $this->adresse_mod = $adresse_mod;

        return $this;
    }

    /**
     * Get adresse_mod
     *
     * @return bool
     */
    public function getadresse_mod()
    {
        return $this->adresse_mod;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="tel", type="string", length=10,nullable=true)
     */
    private $tel;

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Customer
     */
    public function settel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function gettel()
    {
        return $this->tel;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="tel_mod", type="boolean", nullable = true)
     */
    private $tel_mod;

    /**
     * Set tel_mod
     *
     * @param bool $tel_mod
     *
     * @return Customer
     */
    public function settel_mod($tel_mod)
    {
        $this->tel_mod = $tel_mod;

        return $this;
    }

    /**
     * Get tel_mod
     *
     * @return bool
     */
    public function gettel_mod()
    {
        return $this->tel_mod;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="fax", type="string", length=10,nullable=true)
     */
    private $fax;

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Customer
     */
    public function setfax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getfax()
    {
        return $this->fax;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="fax_mod", type="boolean", nullable = true)
     */
    private $fax_mod;

    /**
     * Set fax_mod
     *
     * @param bool $fax_mod
     *
     * @return Customer
     */
    public function setfax_mod($fax_mod)
    {
        $this->fax_mod = $fax_mod;

        return $this;
    }

    /**
     * Get fax_mod
     *
     * @return bool
     */
    public function getfax_mod()
    {
        return $this->fax_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable = true)
     */
    private $email;


    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setemail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getemail()
    {
        return $this->email;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="email_mod", type="boolean", nullable = true)
     */
    private $email_mod;

    /**
     * Set email_mod
     *
     * @param bool $email_mod
     *
     * @return Customer
     */
    public function setemail_mod($email_mod)
    {
        $this->email_mod = $email_mod;

        return $this;
    }

    /**
     * Get email_mod
     *
     * @return bool
     */
    public function getemail_mod()
    {
        return $this->email_mod;
    }

    /**
 * @var string
 *
 * @ORM\Column(name="cp", type="string", length=5, nullable = true)
 */
    private $cP;


    /**
     * Set cP
     *
     * @param string $cP
     *
     * @return Customer
     */
    public function setcP($cP)
    {
        $this->cP = $cP;

        return $this;
    }

    /**
     * Get cP
     *
     * @return string
     */
    public function getcP()
    {
        return $this->cP;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="cP_mod", type="boolean", nullable = true)
     */
    private $cP_mod;

    /**
     * Set fax_mod
     *
     * @param bool $cP_mod
     *
     * @return Customer
     */
    public function setcP_mod($cP_mod)
    {
        $this->cP_mod = $cP_mod;

        return $this;
    }

    /**
     * Get cP_mod
     *
     * @return bool
     */
    public function getcP_mod()
    {
        return $this->cP_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="codeape", type="string", length=5, nullable = true)
     */
    private $codeApe;


    /**
     * Set codeApe
     *
     * @param string $codeApe
     *
     * @return Customer
     */
    public function setcodeApe($codeApe)
    {
        $this->codeApe = $codeApe;

        return $this;
    }

    /**
     * Get codeApe
     *
     * @return string
     */
    public function getcodeApe()
    {
        return $this->codeApe;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="codeape_mod", type="boolean", nullable = true)
     */
    private $codeape_mod;

    /**
     * Set codeape_mod
     *
     * @param bool $codeape_mod
     *
     * @return Customer
     */
    public function setcodeape_mod($codeape_mod)
    {
        $this->codeape_mod = $codeape_mod;

        return $this;
    }

    /**
     * Get codeape_mod
     *
     * @return bool
     */
    public function getcodeape_mod()
    {
        return $this->codeape_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable = true)
     */
    private $ville;


    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Customer
     */
    public function setville($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getville()
    {
        return $this->ville;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="ville_mod", type="boolean", nullable = true)
     */
    private $ville_mod;

    /**
     * Set ville_mod
     *
     * @param bool $ville_mod
     *
     * @return Customer
     */
    public function setville_mod($ville_mod)
    {
        $this->ville_mod = $ville_mod;

        return $this;
    }

    /**
     * Get ville_mod
     *
     * @return bool
     */
    public function getville_mod()
    {
        return $this->ville_mod;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable = true)
     */
    private $pays;

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Customer
     */
    public function setpays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getpays()
    {
        return $this->pays;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="pays_mod", type="boolean", nullable = true)
     */
    private $pays_mod;

    /**
     * Set pays_mod
     *
     * @param bool $pays_mod
     *
     * @return Customer
     */
    public function setpays_mod($pays_mod)
    {
        $this->pays_mod = $pays_mod;

        return $this;
    }

    /**
     * Get pays_mod
     *
     * @return bool
     */
    public function getpays_mod()
    {
        return $this->pays_mod;
    }


    /**
     * @ORM\ManyToOne(targetEntity="SettingsQuality", inversedBy="customers")
     * @ORM\JoinColumn(name="quality_id", referencedColumnName="id",nullable=true)
     */
    private $qualiteClient;

    /**
     * Set qualiteClient
     *
     * @param SettingsQuality $qualiteClient
     *
     * @return Customer
     */
    public function setqualiteClient($qualiteClient)
    {
        $this->qualiteClient = $qualiteClient;

        return $this;
    }

    /**
     * Get qualiteClient
     *
     * @return SettingsQuality
     */
    public function getqualiteClient()
    {
        return $this->qualiteClient;
    }
    /**
     * @var bool
     *
     * @ORM\Column(name="quality_mod", type="boolean", nullable = true)
     */
    private $quality_mod;

    /**
     * Set quality_mod
     *
     * @param bool $quality_mod
     *
     * @return Customer
     */
    public function setquality_mod($quality_mod)
    {
        $this->quality_mod = $quality_mod;

        return $this;
    }

    /**
     * Get quality_mod
     *
     * @return bool
     */
    public function getquality_mod()
    {
        return $this->quality_mod;
    }





    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreation", type="datetime", nullable = true)
     */
    private $dateCreation;

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Customer
     */
    public function setdateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getdateCreation()
    {
        return $this->dateCreation;
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="smallint", nullable = false)
     */
    private $statut;

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Customer
     */
    public function setstatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getstatut()
    {
        return $this->statut;
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
     * Set idcustomer
     *
     * @param string $idcustomer
     *
     * @return Customer
     */
    public function setidCustomer($idcustomer)
    {
        $this->idCustomer = $idcustomer;

        return $this;
    }

    /**
     * Get idcustomer
     *
     * @return string
     */
    public function getidCustomer()
    {
        return $this->idCustomer;
    }

    /**
     * Set idsab
     *
     * @param string $idsab
     *
     * @return Customer
     */
    public function setidsab($idsab)
    {
        $this->idSAB = $idsab;

        return $this;
    }

    /**
     * Get idsab
     *
     * @return string
     */
    public function getidsab()
    {
        return $this->idSAB;
    }


    /**
     * @var bool
     *
     * @ORM\Column(name="exist", type="boolean", nullable = true)
     */
    private $exist;

    /**
     * Set exist
     *
     * @param bool $exist
     *
     * @return Customer
     */
    public function setExist($exist)
    {
        $this->exist = $exist;

        return $this;
    }

    /**
     * Get exist
     *
     * @return bool
     */
    public function getExist()
    {
        return $this->exist;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="coteActivite", type="string", length=1, nullable = true)
     */
    private $coteActivite;

    /**
     * Set coteActivite
     *
     * @param string $coteActivite
     *
     * @return Customer
     */
    public function setcoteActivite($coteActivite)
    {
        $this->coteActivite = $coteActivite;

        return $this;
    }

    /**
     * Get coteActivite
     *
     * @return string
     */
    public function getcoteActivite()
    {
        return $this->coteActivite;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="coteCredit", type="string", length=3, nullable = true)
     */
    private $cotecredit;

    /**
     * Set cotecredit
     *
     * @param string $cotecredit
     *
     * @return Customer
     */
    public function setcotecredit($cotecredit)
    {
        $this->cotecredit = $cotecredit;

        return $this;
    }

    /**
     * Get cotecredit
     *
     * @return string
     */
    public function getcotecredit()
    {
        return $this->cotecredit;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="anomaliesComments", type="text", nullable = true)
     */
    private $anomaliesComments;

    /**
     * Set anomaliesComments
     *
     * @param string $anomaliesComments
     *
     * @return Customer
     */
    public function setanomaliesComments($anomaliesComments)
    {
        $this->anomaliesComments = $anomaliesComments;

        return $this;
    }

    /**
     * Get cotecredit
     *
     * @return string
     */
    public function getanomaliesComments()
    {
        return $this->anomaliesComments;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="formeJuridiqueSab", type="string", length=255, nullable = true)
     */
    private $formeJuridiqueExt;

    /**
     * Set formeJuridiqueExt
     *
     * @param string $formeJuridiqueExt
     *
     * @return Customer
     */
    public function setformeJuridiqueExt($formeJuridiqueExt)
    {
        $this->formeJuridiqueExt = $formeJuridiqueExt;

        return $this;
    }

    /**
     * Get formeJuridiqueExt
     *
     * @return string
     */
    public function getformeJuridiqueExt()
    {
        return $this->formeJuridiqueExt;
    }

}

