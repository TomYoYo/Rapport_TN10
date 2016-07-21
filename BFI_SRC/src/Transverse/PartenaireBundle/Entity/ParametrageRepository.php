<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ParametrageRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('publishedAt' => 'DESC'));
    }
}
