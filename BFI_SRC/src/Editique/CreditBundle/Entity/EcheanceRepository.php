<?php

namespace Editique\CreditBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Editique\CreditBundle\Entity\Credit;

/**
 * EcheanceRepository
 *
 */
class EcheanceRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('datetime' => 'DESC'));
    }

    public function getEcheance4Credit(Credit $oCredit)
    {
        $query = $this
            ->createQueryBuilder('e')
            ->where('e.numDos = :numDos')
            ->andWhere('e.numPret = :numPret')
            ->andWhere('e.numCas = :numCas')
            ->andWhere('e.type in (:type)')
            ->setParameters(
                array(
                    'numDos'=>$oCredit->getNumDos(),
                    'numPret'=>$oCredit->getNumPret(),
                    'numCas'=>'      ',
                    'type'=>array('02', '03', '04')
                )
            )
            ->orderBy('e.numEcheance', 'ASC');

        return $query->getQuery()->getResult();
    }
}
