<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('role' => 'DESC'));
    }
}
