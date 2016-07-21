<?php

namespace Fiscalite\ODBundle\EntityBFI2;

use Doctrine\ORM\EntityRepository;
use BackOffice\ParserBundle\Manager\ParserManager;

/**
 * ActionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationSabRapprochementRepository extends EntityRepository
{
    public function findAll()
    {
        set_time_limit(300);
        return $this->findBy(array(), null, null, null);
    }

    public function myFindAllDQL($datas)
    {
        set_time_limit(300);
 
        $query = $this->createQueryBuilder('o');
        $query->select('o.numcompte, SUM(o.montantmouvement)')
            ->where('o.codeop IS NOT NULL');
   
        if (isset($datas['dateCptDu']) && $datas['dateCptDu']) {
            $query
                ->andWhere($query->expr()->gte('o.datecomptable', ':dateCptDu'))
                ->setParameter(
                    'dateCptDu',
                    ParserManager::transformDateToCYYMMDD($datas['dateCptDu'])
                );
        }
        if (isset($datas['dateCptAu']) && $datas['dateCptAu']) {
            $query
                ->andWhere($query->expr()->lte('o.datecomptable', ':dateCptAu'))
                ->setParameter(
                    'dateCptAu',
                    ParserManager::transformDateToCYYMMDD($datas['dateCptAu'])
                );
        }
      
        if ($datas['codeOpe'] == 'Tous') {
            $query
                ->andWhere(
                    "(o.codeop = '*FB' OR o.codeop = '*TV' OR o.codeop = '*AC' OR o.codeop = '*FD'"
                    . "OR o.codeop = '*BQ' OR o.codeop = '*OD' OR o.codeop = '*PA' OR o.codeop = '*VE')"
                );
        } else {
            $query
                ->andWhere('o.codeop = :co')
                ->setParameter('co', $datas['codeOpe']);
        }
        $query->GroupBy('o.numcompte');
        return $query->getQuery()->getResult();
    }

    public function myFindAllDQLDetail($datas)
    {
        set_time_limit(300);

        $query = $this->createQueryBuilder('o');
        $query->select('o.numcompte, o.montantmouvement, o.datecomptable, o.numerooperation, o.codeop')
            ->where('trim(o.numcompte) = :c')
            ->setParameter('c', $datas['compte']);
   
        if (isset($datas['dateCptDu']) && $datas['dateCptDu']) {
            $query
                ->andWhere($query->expr()->gte('o.datecomptable', ':dateCptDu'))
                ->setParameter(
                    'dateCptDu',
                    ParserManager::transformDateToCYYMMDD($datas['dateCptDu'])
                );
        }
        if (isset($datas['dateCptAu']) && $datas['dateCptAu']) {
            $query
                ->andWhere($query->expr()->lte('o.datecomptable', ':dateCptAu'))
                ->setParameter(
                    'dateCptAu',
                    ParserManager::transformDateToCYYMMDD($datas['dateCptAu'])
                );
        }
      
        if ($datas['codeOpe'] == 'Tous') {
            $query
            ->andWhere(
                "(o.codeop = '*FB' OR o.codeop = '*TV' OR o.codeop = '*AC' OR o.codeop = '*FD'"
                . "OR o.codeop = '*BQ' OR o.codeop = '*OD' OR o.codeop = '*PA' OR o.codeop = '*VE')"
            );
        } else {
            $query
                ->andWhere('o.codeop = :co')
                ->setParameter('co', $datas['codeOpe']);
        }
        return $query->getQuery()->getResult();

    }
}