<?php

namespace Editique\RIBBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rib
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Rib
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
     * @ORM\Column(name="holder", type="string", length=32)
     */
    private $holder;

    /**
     * @var string
     *
     * @ORM\Column(name="holderSuite", type="string", length=32)
     */
    private $holderSuite;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="addressSuite", type="string", length=255)
     */
    private $addressSuite;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNumber", type="string", length=20)
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=40)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=12)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="idClient", type="string", length=7)
     */
    private $idClient;

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
     * Set holder
     *
     * @param string $holder
     * @return Rib
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
    }

    /**
     * Get holder
     *
     * @return string
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * Set holderSuite
     *
     * @param string $holderSuite
     * @return Rib
     */
    public function setHolderSuite($holderSuite)
    {
        $this->holderSuite = $holderSuite;

        return $this;
    }

    /**
     * Get holderSuite
     *
     * @return string
     */
    public function getHolderSuite()
    {
        return $this->holderSuite;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Rib
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set addressSuite
     *
     * @param string $addressSuite
     * @return Rib
     */
    public function setAddressSuite($addressSuite)
    {
        $this->addressSuite = $addressSuite;

        return $this;
    }

    /**
     * Get addressSuite
     *
     * @return string
     */
    public function getAddressSuite()
    {
        return $this->addressSuite;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     * @return Rib
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set iban
     *
     * @param string $iban
     * @return Rib
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set bic
     *
     * @param string $bic
     * @return Rib
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     * @return Rib
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
}
