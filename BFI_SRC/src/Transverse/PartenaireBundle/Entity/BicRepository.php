<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BicRepository extends EntityRepository
{
    public function findBic()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.bic')
            ->getQuery();

        return $qb->getArrayResult();
    }
}
