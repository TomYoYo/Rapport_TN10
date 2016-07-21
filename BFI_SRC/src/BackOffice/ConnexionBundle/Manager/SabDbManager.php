<?php

namespace BackOffice\ConnexionBundle\Manager;

use Doctrine\ORM\EntityManager;

class SabDbManager
{
    private $entityManager;
    private $exeTime;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getExeTime()
    {
        return $this->exeTime;
    }

    public function getSimpleSqlResult($req)
    {
        $p = $this->entityManager
            ->getConnection()
            ->prepare($req)
            ;
        $p->execute();

        return $p->fetch();
    }

    public function chronoSimpleSqlResult($req)
    {
        $start = microtime(true);
        $res = $this->getSimpleSqlResult($req);
        $end = microtime(true);

        $this->exeTime = round(($end-$start), 4);

        return $res;
    }

    /**
     * Execute une requete native
     * @param type $column peut etre un tableau ou un scalaire
     * @param type $table
     * @param type $whereParameters : DANGER
     * @return type tableau ou scalaire selon le type de la variable column
     */
    public function getDirectSqlResult($column, $table, $whereParameters)
    {
        if (is_array($column)) {
            $query = "SELECT " . implode(',', $column) . " FROM " . $table;
        } else {
            $query = "SELECT " . $column . " FROM " . $table;
        }

        $i = 0;

        foreach ($whereParameters as $parameter => $value) {
            if ($i == 0) {
                $query .= ' WHERE ';
            } else {
                $query .= ' AND ';
            }

            $query .= $parameter . ' = ' . $value;
            $i++;
        }

        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();

        if (is_array($column)) {
            return $req->fetch();
        }

        $res = $req->fetch();

        return $res[$column];
    }
}
