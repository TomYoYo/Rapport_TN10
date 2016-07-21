<?php

namespace Fiscalite\ODBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fiscalite\ODBundle\Form\OperationType;
use Fiscalite\ODBundle\Entity\Operation;
use Fiscalite\ODBundle\EntityBFI2\OperationSabRapprochement;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use BackOffice\ParserBundle\Manager\ParserManager;

/**
 * Operation controller.
 *
 */
class RapprochementController extends Controller
{
    /**
    * Affiche page de rapprochement avec le formulaire.
    *
    * @Template("FiscaliteODBundle:Rapprochement:Rapprochement.html.twig")
    */
    public function rapprochementAction(Request $request)
    {
        $listCodeOpe = array(
            "Tous" => "Tous",
            "*FB" => "*FB",
            "*TV" => "*TV",
            "*AC" => "*AC",
            "*FD" => "*FD",
            "*BQ" => "*BQ",
            "*OD" => "*OD",
            "*PA" => "*PA",
            "*VE" => "*VE",
        );

        return array(
            'listCodeOpe' => $listCodeOpe
        );
    }
    
    /**
    * Lists searched Operations.
    *
    * @Template("FiscaliteODBundle:Rapprochement:Rapprochement.html.twig")
    */
    public function rapprochementSearchAction(Request $request)
    {
        $listCodeOpe = array(
            "Tous" => "Tous",
            "*FB" => "*FB",
            "*TV" => "*TV",
            "*AC" => "*AC",
            "*FD" => "*FD",
            "*BQ" => "*BQ",
            "*OD" => "*OD",
            "*PA" => "*PA",
            "*VE" => "*VE",
        );
          
        $em      = $this->getDoctrine()->getManager();
        $em2      = $this->getDoctrine()->getManager('bfi2');
        //dans le form, les input on tous name= search['kkechose'], on les récupères tous
        //datas correspond donc aux données saisient par l'user
        $datas   = $request->request->get('search');
        $session = new Session();
        //on met dans la session nos critères de recherche (pour la pagination après)
        $session->set('datas/OD', $datas);
        //on recherche dans la bdd en fonction de notre recherche
        //ici la méthode search a été ajouté dans OperationRepository qui hérite
        //directement de la classe officielle EntityRepository
        //ce search prend en compte tous les filtres envoyé depuis le form.
        $entities = $em->getRepository('FiscaliteODBundle:Operation')->searchRapprochement($datas);
        $entitiesSab = $em2->getRepository('FiscaliteODBundle:OperationSabRapprochement')->myFindAllDQL($datas);
      
        
        //on trim car les num compte sur SAB ont des espaces en trop
        array_walk_recursive($entitiesSab, function (&$v, $k) {
            $v = trim($v);
            //on transforme le montant en float, car il était en string et
            //on multiplie par -1 car SAB "inverse" les valeurs négatives et positives
            if ($k == '1') {
                $v = (float)($v)*(-1);
            }
        });
        //on trim sur BFI car il y a parfois des espaces en trop
        array_walk_recursive($entities, function (&$v, $k) {
            $v = trim($v);
            //on transforme le montant en float, car il était en string
            if ($k == '1') {
                $v = (float)($v);
            }
        });
        $entitiesGrp = array();
        //On regroupe les numéros de compte maitenant qu'on les a trim sur BFI
        //(car le 'group by' sur du TRIM en SQL pose pb)
        foreach ($entities as $row) {
            $entitiesGrp[$row['compte']]['compte'] = $row['compte'];
            if (!isset($entitiesGrp[$row['compte']][0])) {
                $entitiesGrp[$row['compte']][] += $row[1];
            } else {
                $entitiesGrp[$row['compte']][0] += $row[1];
            }
        }
        //$entities groupée et trimée
        $entitiesGrp = array_values($entitiesGrp);
        
        $entitiesGrpAcSab = array();
        //Pour chaque entité BFI on ajoute une 3eme colonne au tableau
        //avec la valeur de SAB (si compte = numcompte biensur)
        foreach ($entitiesGrp as $key => $row) {
            $entitiesGrpAcSab[$row['compte']]['compte'] = $row['compte'];
            $entitiesGrpAcSab[$row['compte']]['0'] = $row[0];
            foreach ($entitiesSab as $keySab => $rowSab) {
                if ($row['compte'] == $rowSab['numcompte']) {
                    if (isset($rowSab[1])) {
                        $entitiesGrpAcSab[$row['compte']][] += $rowSab[1];
                    }
                }
            }
        }
        //entitiesGrp avec les valeurs de SAB
        $entitiesGrpAcSab = array_values($entitiesGrpAcSab);
         
        //on nettoie les tableaux qu'on n'utilisera plus pour économiser de la mémoire
        $entitiesGrp = array();
        $entitiesSab = array();
        $entities = array();
        
        $entitiesGrpAcSabAcDiff = array();
        //On fait la différence pour vérifier si il y a un écart entre SAB et BFI
        foreach ($entitiesGrpAcSab as $key => $row) {
            $entitiesGrpAcSabAcDiff[$row['compte']]['compte'] = $row['compte'];
            $entitiesGrpAcSabAcDiff[$row['compte']]['0'] = round($row[0], 2);
            if (isset($row[1])) {
                $entitiesGrpAcSabAcDiff[$row['compte']]['1'] = round($row[1], 2);
                if ($row[1] <= $row[0]) {
                     $entitiesGrpAcSabAcDiff[$row['compte']][] = abs(round($row[0] - $row[1], 2));
                } else {
                    $entitiesGrpAcSabAcDiff[$row['compte']][] = abs(round($row[1] - $row[0], 2));
                }
            }
        }
        //on ajouter une 4eme valeur avec la différence.
        $entitiesGrpAcSabAcDiff = array_values($entitiesGrpAcSabAcDiff);
        
        
        if (isset($datas['trie']) and $datas['trie'] == 'compte') {
            usort($entitiesGrpAcSabAcDiff, function ($a, $b) {
                return strcmp($a["compte"], $b["compte"]);
            });
        } else {
            //On met les valeurs ac le plus d'écart en tête de tableau
            usort($entitiesGrpAcSabAcDiff, function ($a, $b) {
                if (isset($a[2]) and isset($b[2])) {
                    if ($a[2]==$b[2]) {
                        return 0;
                    }
                    return $a[2] < $b[2]?1:-1;
                } else {
                    return $a > $b?1:-1;
                }
            });
        }
        return array(
            'entities' => $entitiesGrpAcSabAcDiff,
            'datas'    => $datas,
            'listCodeOpe' => $listCodeOpe
        );

    }
    
    
     /**
     * List detailed operation
     *
     * @Template("FiscaliteODBundle:Rapprochement:Rapprochement.html.twig")
     */
    public function rapprochementDetailAction(Request $request)
    {
        $listCodeOpe = array(
            "Tous" => "Tous",
            "*FB" => "*FB",
            "*TV" => "*TV",
            "*AC" => "*AC",
            "*FD" => "*FD",
            "*BQ" => "*BQ",
            "*OD" => "*OD",
            "*PA" => "*PA",
            "*VE" => "*VE",
        );
        
        $em      = $this->getDoctrine()->getManager();
        $em2      = $this->getDoctrine()->getManager('bfi2');
        $detail   = $request->request->get('detail');
  
        $datas['codeOpe'] = $detail['codeOpe'];
        $datas['dateCptDu'] = $detail['dateCptDu'];
        $datas['dateCptAu'] = $detail['dateCptAu'];

        $session = new Session();
        $session->set('detail/OD', $detail);
        $entitiesDetail = $em->getRepository('FiscaliteODBundle:Operation')
                ->searchDetail($detail);
        $entitiesSabDetail = $em2->getRepository('FiscaliteODBundle:OperationSabRapprochement')
                ->myFindAllDQLDetail($detail);
        
  
       //on transforme le montant en float, car il était en string et on multiplie par -1
       //car SAB inverse les valeurs négatives et positives ...
        array_walk_recursive(
            $entitiesSabDetail,
            function (&$v, $k) {
                if ($k == 'montantmouvement') {
                    $v = (float)($v)*(-1);
                }
            }
        );
        
        //On fait la somme des montants qui ont le même numPieceTech sur BFI
        $entitiesDetailGrp = array();
        foreach ($entitiesDetail as $row) {
            $entitiesDetailGrp[$row['numPieceTech']]['numPieceTech'] = $row['numPieceTech'];

            //on fait la somme des montant en séparant débits et crédits
            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['montantcredit']) and $row['montant'] > 0) {
                $entitiesDetailGrp[$row['numPieceTech']]['montantcredit'] = $row['montant'];
            } elseif ($row['montant'] > 0) {
                $entitiesDetailGrp[$row['numPieceTech']]['montantcredit'] += $row['montant'];
            }

            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['montantdebit']) and $row['montant'] < 0) {
                $entitiesDetailGrp[$row['numPieceTech']]['montantdebit'] = $row['montant'];
            } elseif ($row['montant'] < 0) {
                $entitiesDetailGrp[$row['numPieceTech']]['montantdebit'] += $row['montant'];
            }

            //on garde les autres valeurs car on utilisera désormais ce array comme référence
            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['dateCpt'])) {
                $entitiesDetailGrp[$row['numPieceTech']]['dateCpt'] = $row['dateCpt'];
            }
            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['numPiece'])) {
                $entitiesDetailGrp[$row['numPieceTech']]['numPiece'] = $row['numPiece'];
            }
            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['codeOpe'])) {
                $entitiesDetailGrp[$row['numPieceTech']]['codeOpe'] = $row['codeOpe'];
            }
            if (!isset($entitiesDetailGrp[$row['numPieceTech']]['compte'])) {
                $entitiesDetailGrp[$row['numPieceTech']]['compte'] = $row['compte'];
            }
          
        }
        $entitiesDetailGrp = array_values($entitiesDetailGrp);
     
         //On fait la somme des montants qui ont le même numerooperation sur SAB
         //(numerooperation correspond au numPieceTech du BFI)
        $entitiesSabDetailGrp = array();
        foreach ($entitiesSabDetail as $row) {
            $entitiesSabDetailGrp[$row['numerooperation']]['numerooperation'] = $row['numerooperation'];
           
            //on sépare débit et crédit
            if (!isset($entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementdebit'])
                    and $row['montantmouvement'] < 0) {
                $entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementdebit'] = $row['montantmouvement'];
            } elseif ($row['montantmouvement'] < 0) {
                $entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementdebit'] += $row['montantmouvement'];
            }
            if (!isset($entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementcredit'])
                    and $row['montantmouvement'] > 0) {
                $entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementcredit'] = $row['montantmouvement'];
            } elseif ($row['montantmouvement'] > 0) {
                $entitiesSabDetailGrp[$row['numerooperation']]['montantmouvementcredit'] += $row['montantmouvement'];
            }

            if (!isset($entitiesSabDetailGrp[$row['numerooperation']]['numcompte'])) {
                $entitiesSabDetailGrp[$row['numerooperation']]['numcompte'] = trim($row['numcompte']);
            }
            if (!isset($entitiesSabDetailGrp[$row['numerooperation']]['datecomptable'])) {
                $entitiesSabDetailGrp[$row['numerooperation']]['datecomptable'] = trim($row['datecomptable']);
            }
            if (!isset($entitiesSabDetailGrp[$row['numerooperation']]['codeop'])) {
                $entitiesSabDetailGrp[$row['numerooperation']]['codeop'] = trim($row['codeop']);
            }

        }
        $entitiesSabDetailGrp = array_values($entitiesSabDetailGrp);
        
      
         
         
        //on fusionne les 2 tableaux avec comme point de jointure numPieceTech
        //côté BFI et numerooperation côté SAB
        $entitiesDetailGrpAcSab = array();
        //on ajoute une autre colonne au tableau BFI avec la valeur des montants de SAB
        //(si numPieceTech = numerooperation biensur)
        foreach ($entitiesDetailGrp as $key => $row) {
            //on profite de la récup de données pr renomer numPieceTech par numPieceSAB
            //sur le nouveau tableau pour ne pas nous embrouiller
            $entitiesDetailGrpAcSab[$row['numPieceTech']]['numPieceSAB'] = $row['numPieceTech'];

            //on initialise le solde BFI si il n'existe pas
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeBFI'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeBFI'] = 0;
            }
            //on initialise le solde SAB si il n'existe pas
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeSAB'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeSAB'] = 0;
            }

            //on initialise l'écart si il n'existe pas
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['ecart'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['ecart'] = 0;
            }

            if (isset($row['montantdebit'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['montantdebitBFI'] = $row['montantdebit'];
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeBFI'] +=  $row['montantdebit'];
            }
            if (isset($row['montantcredit'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['montantcreditBFI'] = $row['montantcredit'];
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeBFI'] +=  $row['montantcredit'];
            }

            //on garde les autres valeurs car on utilisera désormais ce array comme référence
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['dateCpt'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['dateCpt'] = $row['dateCpt'];
            }
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['numPiece'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['numPieceBFI'] = $row['numPiece'];
            }
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['codeOpe'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['codeOpe'] = $row['codeOpe'];
            }
            if (!isset($entitiesDetailGrpAcSab[$row['numPieceTech']]['compte'])) {
                $entitiesDetailGrpAcSab[$row['numPieceTech']]['compte'] = $row['compte'];
            }


            //on groupe les montants SAB avec notre tableau BFI
            foreach ($entitiesSabDetailGrp as $keySab => $rowSab) {
                if ($row['numPieceTech'] == $rowSab['numerooperation']) {
                    if (isset($rowSab['montantmouvementdebit'])) {
                        $entitiesDetailGrpAcSab[$row['numPieceTech']]['montantdebitSAB'] =
                                $rowSab['montantmouvementdebit'];
                        $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeSAB'] +=
                                $rowSab['montantmouvementdebit'];
                    }
                    if (isset($rowSab['montantmouvementcredit'])) {
                        $entitiesDetailGrpAcSab[$row['numPieceTech']]['montantcreditSAB'] =
                                $rowSab['montantmouvementcredit'];
                        $entitiesDetailGrpAcSab[$row['numPieceTech']]['soldeSAB'] +=
                                $rowSab['montantmouvementcredit'];
                    }
                }
            }
        }
        $entitiesDetailGrpAcSab = array_values($entitiesDetailGrpAcSab);

   
        //on va chercher les valeurs qui sont dans SAB mais pas dans BFI,
        //car notre group BY avait pour référence les valeurs de BFI et il a y a des ano
        foreach ($entitiesSabDetailGrp as $aV) {
            if (isset($aV['numerooperation'])) {
                $aTmp1[] = $aV['numerooperation'];
            }
        }
        foreach ($entitiesDetailGrp as $aV) {
            if (isset($aV['numPieceTech'])) {
                 $aTmp2[] = $aV['numPieceTech'];
            }
        }
        if (isset($aTmp1) and isset($aTmp2)) {
            $numpieceSabPasDansBfi  = array_diff($aTmp1, $aTmp2);
        }
     

        foreach ($entitiesSabDetailGrp as $keySab => $rowSab) {
            foreach ($numpieceSabPasDansBfi as $key => $row) {
                if ($rowSab['numerooperation'] == $row) {
                    if (isset($rowSab['montantmouvementdebit'])) {
                        $montantmouvementdebit = $rowSab['montantmouvementdebit'];
                    } else {
                        $montantmouvementdebit = 0;
                    }
                    if (isset($rowSab['montantmouvementcredit'])) {
                        $montantmouvementcredit = $rowSab['montantmouvementcredit'];
                    } else {
                        $montantmouvementcredit = 0;
                    }
                        
                    //on calcule le soldeSAB
                    $soldeSAB = $montantmouvementdebit + $montantmouvementcredit;
                          
                    $datecomptable =$rowSab['datecomptable'];
                    $newdata =  array (
                        'numPieceSAB' => $rowSab['numerooperation'],
                        'compte' => $rowSab['numcompte'],
                        'soldeBFI' => null,
                        'soldeSAB' => $soldeSAB,
                        'ecart' => 0,
                        'montantdebitBFI' => null,
                        'dateCpt' => ParserManager::transformDateSABToYdm($rowSab['datecomptable']),
                        'numPieceBFI' => null,
                        'codeOpe' => $rowSab['codeop'],
                        'montantdebitSAB' => $montantmouvementdebit,
                        'montantcreditSAB' => $montantmouvementcredit,
                        'montantcreditBFI' => null
                    );
                    array_push($entitiesDetailGrpAcSab, $newdata);
                }
                        
            }
        }
        
        //on vide entitiesSabDetailGrp et $entitiesDetailGrp etc car on ne les
        //utilisera plus et on économise ainsi de la mémoire
        $entitiesSabDetailGrp = array();
        $entitiesDetailGrp = array();
        $entitiesDetail = array();
        $entitiesSabDetail = array();
        
        //on calcule l'écart
        $entitiesDetailGrpAcSabDiff = array();
        foreach ($entitiesDetailGrpAcSab as $row) {
            if ($row['soldeSAB'] <=  $row['soldeBFI']) {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['ecart'] =
                        abs(round($row['soldeBFI'] -  $row['soldeSAB'], 2));
            } else {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['ecart'] =
                        abs(round($row['soldeSAB'] -  $row['soldeBFI'], 2));
            }
            
            //on récupère les autres valeurs
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['numPieceSAB'] = $row['numPieceSAB'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['soldeBFI'] = $row['soldeBFI'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['soldeSAB'] = $row['soldeSAB'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['numPieceBFI'] = $row['numPieceBFI'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['compte'] = $row['compte'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['dateCpt'] = $row['dateCpt'];
            $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['codeOpe'] = $row['codeOpe'];
            if (isset($row['montantdebitBFI'])) {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantdebitBFI'] = $row['montantdebitBFI'];
            } else {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantdebitBFI'] = 0;
            }
            if (isset($row['montantdebitSAB'])) {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantdebitSAB'] = $row['montantdebitSAB'];
            } else {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantdebitSAB'] = 0;
            }

            if (isset($row['montantcreditBFI'])) {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantcreditBFI'] = $row['montantcreditBFI'];
            } else {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantcreditBFI'] = 0;
            }

            if (isset($row['montantcreditSAB'])) {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantcreditSAB'] = $row['montantcreditSAB'];
            } else {
                $entitiesDetailGrpAcSabDiff[$row['numPieceSAB']]['montantcreditSAB'] = 0;
            }

        }
        $entitiesDetailGrpAcSabDiff = array_values($entitiesDetailGrpAcSabDiff);
        

        return array(
           'entitiesDetail' => $entitiesDetailGrpAcSabDiff,
           'detail'    => $detail,
           'datas' => $datas,
           'listCodeOpe' => $listCodeOpe
        );
        
    }
    
    /**
    * 
    *
    * @Template()
    */
    public function exportAction()
    {
        return array();
    }
    
    public function rapprochementExportingAction(Request $request)
    {
        $datas = $this->rapprochementSearchAction($request);
        $entities = $datas['entities'];

        return $this->getCSV($entities);
    }
    
    private function getCSV($e)
    {
        $response = $this->render('FiscaliteODBundle:Export:exportRapprochement.csv.twig', array('entities' => $e));
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . date('Ymd_his_') . 'EXPORT_RAPPRO_OD.csv"'
        );
        return $response;
    }
}
