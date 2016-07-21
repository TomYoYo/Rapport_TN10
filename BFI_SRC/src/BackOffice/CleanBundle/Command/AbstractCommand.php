<?php

namespace BackOffice\CleanBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use BackOffice\ParserBundle\Manager\ParserManager;

abstract class AbstractCommand extends ContainerAwareCommand
{
    protected $lm; // log manager
    protected $em; //entite manager
    protected $debug = false;
    protected $nolog = false;

    /**
     * Parcourt $dossier et supprime les fichiers de plus de $tps jours
     * @param type $dossier : chemin du dossier à parcourir
     * @param type $tps : nombre de jour de vieillesse
     */
    protected function deleteOldFile($dossier, $tps, $unit = 'day')
    {
        foreach (glob($dossier.'/*') as $filename) {
            if (is_dir($filename)) {
                $this->deleteOldFile($filename, $tps);
            } elseif (filemtime($filename) < strtotime("-$tps $unit")) {
                exec('rm "'.$filename.'"');
            }
        }
    }

    /**
     * Supprime les données de l'entite selon son champs date et le nb de jour
     * @param string $ns namespace de l'entite
     * @param string $timeField : champ date de l'entite
     * @param int $tps : nb de unit of time
     * @param string $unit : day or month
     * @return int nb of deleted lines
     */
    protected function deleteOldData($ns, $timeField, $tps, $unit = 'day')
    {
        $query = $this->em->createQueryBuilder('e');

        $query
            ->delete($ns, 'e')
            ->where($query->expr()->lte("e.$timeField", ':time'))
            ->setParameter(
                'time',
                ParserManager::transformDate(date('d/m/Y', strtotime("-$tps $unit"))),
                \Doctrine\DBAL\Types\Type::DATETIME
            )
        ;

        return $query->getQuery()->getResult();
    }

    protected function logIt($logMsg, $type = 'file')
    {
        if ($type == 'data') {
            $action = 'Nettoyage en base';
        } else {
            $action = 'Nettoyage de fichier';
        }

        if (!$this->nolog) {
            $this->lm->addInfo($logMsg, 'BackOffice > Nettoyage', $action);
        }
        if ($this->debug) {
            echo $logMsg."\n";
        }
    }
}
