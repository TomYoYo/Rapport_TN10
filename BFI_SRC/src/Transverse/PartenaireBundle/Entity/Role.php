<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table("TRANSVERSE_ROLE")
 * @ORM\Entity
 */
class Role
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
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="nomRole", type="string", length=255, nullable=true)
     */
    private $nomRole;

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
     * Set role
     *
     * @param string $role
     *
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }
    
        /**
     * Set nomRole
     *
     * @param string $role
     *
     * @return Role
     */
    public function setNomRole($nomRole)
    {
        $this->nomRole = $nomRole;
    
        return $this;
    }

    /**
     * Get nomRole
     *
     * @return string 
     */
    public function getNomRole()
    {
        return $this->nomRole;
    }
}
