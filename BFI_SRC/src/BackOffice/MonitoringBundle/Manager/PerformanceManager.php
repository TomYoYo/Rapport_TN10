<?php

namespace BackOffice\MonitoringBundle\Manager;

use BackOffice\ConnexionBundle\Manager\SabDbManager;
use BackOffice\ConnexionBundle\Manager\SabSFTPManager;

/**
 * Description of LogManager
 *
 * @author j.david
 */
class PerformanceManager
{
    public $sabDbManager;
    public $sabSFTPManager;
    public $nomProcessJour;
    public $nomProcessJourBD;

    public function __construct(SabDbManager $sabDbManager, SabSFTPManager $sabSFTPManager, $jour, $jourbd)
    {
        $this->sabDbManager     = $sabDbManager;
        $this->sabSFTPManager   = $sabSFTPManager;
        $this->nomProcessJour   = $jour;
        $this->nomProcessJourBD = $jourbd;
    }

    public function getNbProduitVendu()
    {
        $req = 'SELECT count(*) as NB FROM ZCOMPTE0';
        $res = $this->sabDbManager->chronoSimpleSqlResult($req);
        $exeTime = $this->sabDbManager->getExeTime();

        return array($res['NB'], $exeTime);
    }

    public function getNbClient()
    {
        $req = '
            SELECT count(*) as NB
            FROM SAB139.ZDWHCPT0, SAB139.ZDWHCLI0, SAB139.ZDWH0020, SAB139.ZPLAN0
            Where ZDWHCLI0.DWHCLICLI= ZDWHCPT0.DWHCPTPPAL
                  and ZDWHCPT0.DWHCPTDTX = ZDWHCLI0.DWHCLIDTX and ZDWHCLI0.DWHCLIDTX = ZDWHCLI0.DWHCLIDSY
                  and ZDWHCPT0.DWHCPTRUB like \'251%\'
                  and ZDWH0020.DWH002APE = ZDWHCLI0.DWHCLIREG
                  and ZDWHCPT0.DWHCPTRUB = ZPLAN0.PLANCOOBL
                ';

        $res = $this->sabDbManager->getSimpleSqlResult($req);

        return $res['NB'];
    }

    public function getNbOperation()
    {
        $req = 'SELECT count(*)  as NB FROM ZDWHOPE0';

        $res = $this->sabDbManager->getSimpleSqlResult($req);

        return $res['NB'];
    }

    public function getPerfJOUR()
    {
        return (int)$this->sabSFTPManager->getPerfUniJob($this->nomProcessJour);
    }

    public function getPerfJOURBD()
    {
        return (int)$this->sabSFTPManager->getPerfUniJob($this->nomProcessJourBD);
    }
}
