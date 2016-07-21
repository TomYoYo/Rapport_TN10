<?php

namespace Editique\CreditBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Editique\CreditBundle\Entity\Credit;

/**
 * CreditRepository
 *
 */
class CreditRepository extends EntityRepository
{
    public function search($datas)
    {
        $query = $this
            ->createQueryBuilder('e')
            ->where('e.id IS NOT NULL');

        if (isset($datas['numDos']) && $datas['numDos'] != "") {
            $query
                ->andWhere('e.numDos = :nd')
                ->setParameter('nd', $datas['numDos']);
        }
        if (isset($datas['numPret']) && $datas['numPret']) {
            $query
                ->andWhere('e.numPret = :np')
                ->setParameter('np', $datas['numPret']);
        }
        
        $query->orderBy('e.numDos', 'ASC');

        return $query->getQuery()->getResult();
    }
}
