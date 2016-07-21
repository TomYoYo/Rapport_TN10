<?php

namespace Fiscalite\ODBundle\Manager;

use Fiscalite\ODBundle\Entity\Mouvement;
use Fiscalite\ODBundle\Entity\Operation;
use Fiscalite\ODBundle\Entity\Action;
use BackOffice\ActionBundle\Entity\Action as ActionTrigger;

/**
 * Description of ODManager
 *
 * @author d.briand
 */
class ODManager
{
    public $logManager;
    public $entityManager;
    public $parserManager;
    public $iteration = 0;

    private $numMvmt = 1;

    public function __construct($lm, $em, $pm)
    {
        $this->logManager    = $lm;
        $this->entityManager = $em->getManager('bfi');
        $this->parserManager = $pm;
    }

    public function getData($request)
    {
        $dataOperation = $request->request->get("od_odbundle_operation");
        $mvmtsData['idOpe']      = $request->request->get("idOpe");
        $mvmtsData['compte']     = $request->request->get("compte");
        $mvmtsData['type']       = $request->request->get("type");
        $mvmtsData['montant']    = $request->request->get("montant");
        $mvmtsData['libelle']    = $request->request->get("libelle");
        $mvmtsData['codeBudget'] = $request->request->get("codeBudget");

        return array($dataOperation, $mvmtsData);
    }

    public function getMvmtCollection($mvmtsData, $operation)
    {
        $arrMvmts = array();
        
        foreach ($mvmtsData['compte'] as $compteMvmt) {
            if ($compteMvmt) {
                $mouvement = $this->addMouvement($mvmtsData, $operation);

                $arrMvmts[] = $mouvement;
            }
            $this->iteration++;
        }

        return $arrMvmts;
    }

    public function addMouvement($mvmtsData, $operation)
    {
        $mouvement = new Mouvement();

        $mouvement
            ->setNumMvmt($this->numMvmt)
            ->setCompte($mvmtsData['compte'][$this->iteration])
            ->setMontant($this->formatMontant($mvmtsData))
            ->setLibelle($mvmtsData['libelle'][$this->iteration])
            ->setCodeBudget($mvmtsData['codeBudget'][$this->iteration])
            ->setOperation($operation);

        $this->numMvmt++;
        return $mouvement;
    }

    public function checkAccountNumbers($arrMvmts)
    {
        foreach ($arrMvmts as $mvmt) {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT COMPTECOM FROM ZCOMPTE0 WHERE COMPTECOM = '" . $mvmt->getCompte() . "'");

            $stmt->execute();
            $res = $stmt->fetchAll();

            if (!$res) {
                return false;
            }
        }

        return true;
    }
    
    public function checkTiers($operation)
    {
        if (trim($operation->getTiers()) != '') {
            $stmt = $this->entityManager
                ->getConnection()
                ->prepare("SELECT CLIENACLI FROM ZCLIENA0 WHERE CLIENACLI = '" . $operation->getTiers() . "'");

            $stmt->execute();
            $res = $stmt->fetchAll();

            if (!$res) {
                return false;
            }
        }

        return true;
    }

    public function checkBalanceOperation($arrMvmts)
    {
        $total = 0;
        foreach ($arrMvmts as $mvmt) {
            $total += $mvmt->getMontant();
        }

        // Merci le binaire ! Dans de nombreux cas (soustraction/division) le cumul de nombres à virgule n'est pas bon
        // Au lieu de retourner 0 il peut retourner 0.000...000001
        if ($total < -0.001 || $total > 0.001) {
            return false;
        }

        return true;
    }

    public function checkAuthorisedJC($operation)
    {
        $jcAutorisee = $this->entityManager->getRepository('FiscaliteODBundle:Operation')->jcAutorise();
        if (!$jcAutorisee && $operation->getIsComplementaryDay() == '1') {
            return false;
        }

        return true;
    }

    public function persistAndFlushFullOperation($operation, $arrMvmts)
    {
        // on supprime les eventuels anciens mouvements
        if (!empty($operation->getMouvements())) {
            foreach ($operation->getMouvements() as $mvt) {
                $this->entityManager->remove($mvt);
            }
        }
        $this->entityManager->flush();

        // on persiste l'operation et ses mouvements
        $this->entityManager->persist($operation);
        foreach ($arrMvmts as $mvmt) {
            $idOpe = $operation->getCodeOpe().$operation->getCodeEve().$operation->getNumPiece().$mvmt->getNumMvmt();
            $mvmt->setIdOpe($idOpe);
            $this->entityManager->persist($mvmt);
        }
        $this->entityManager->flush();
    }

    public function addOperation($data, $user)
    {
        $operation = new Operation();
        $statut    = $this->entityManager->getRepository('FiscaliteODBundle:Statut')->findOneByIdStatut("VAL");

        $operation
            ->setProfil($user)
            ->setDateCpt($this->parserManager->transformDate($data['dateCpt']))
            ->setDevise($data['devise'])
            ->setDateVal($this->parserManager->transformDate($data['dateVal']))
            ->setCodeOpe($data['codeOpe'])
            ->setCodeEve($data['codeEve'])
            ->setRefLet($data['refLet'])
            ->setRefAnalytique($data['refAnalytique'])
            ->setTiers($data['tiers'])
            ->setIsComplementaryDay($data['isComplementaryDay'])
            ->setStatut($statut)
            ->setDateSai(new \Datetime())
            ->setDateStatut(new \Datetime());

        return $operation;
    }

    public function addAction($libelle, $operation, $user)
    {
        $action = new Action();

        $action
            ->setLibelleAction($libelle)
            ->setOperation($operation)
            ->setProfil($user)
            ->setDateAction();

        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }

    public function addActionTrigger($numCpt = 0)
    {
        $action = new ActionTrigger();

        $action
            ->setType('OD')
            ->setModule('CRE')
            ->setNumCpt($numCpt);

        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }

    public function majStatut($operation, $user, $statutCode = 'VAL')
    {
        $statut = $this->entityManager->getRepository('FiscaliteODBundle:Statut')->findOneByIdStatut($statutCode);

        $operation
            ->setStatutPrec($operation->getStatut())
            ->setDateStatutPrec($operation->getDateStatut())
            ->setStatut($statut)
            ->setDateStatut(new \Datetime())
            ->setProfil($user);

        $this->entityManager->persist($operation);
        $this->entityManager->flush();
    }
    
    public function majValideur($operation, $user)
    {
        $operation
            ->setValideur($user)
            ->setDateValid(new \Datetime());

        $this->entityManager->persist($operation);
        $this->entityManager->flush();
    }

    public function getCodesOpe()
    {
        $stmt = $this->entityManager
            ->getConnection()
            ->prepare("SELECT SCHEMAOPE FROM SAB139.ZSCHEMA0 WHERE SCHEMAOPE LIKE '*%' GROUP BY SCHEMAOPE")
        ;
        $stmt->execute();
        $res = $stmt->fetchAll();
        $list = array();
        
        $list[''] = '';
        foreach ($res as $code) {
            $list[$code['SCHEMAOPE']] = $code['SCHEMAOPE'];
        }
        
        return $list;
    }

    private function formatMontant($mvmtsData)
    {
        $montant = str_replace(array(',', ' '), array('.', ''), $mvmtsData['montant'][$this->iteration]);

        if ($mvmtsData['type'][$this->iteration] == 'deb') {
            $montant = "-" . $montant;
        }

        return $montant;
    }
}
