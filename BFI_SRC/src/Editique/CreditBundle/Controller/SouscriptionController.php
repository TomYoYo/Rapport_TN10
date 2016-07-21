<?php

namespace Editique\CreditBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Editique\MasterBundle\Entity\Souscription;
use Editique\CreditBundle\Entity\SouscriptionEditique;
use BackOffice\ActionBundle\Entity\Action;

class SouscriptionController extends Controller
{
    public function indexAction(Request $request)
    {
        $entities = false;
        $numDos = $request->request->get('numDos');

        if ($numDos !== null) {
            $repo = $this->getDoctrine()->getManager('bfi2')->getRepository('EditiqueCreditBundle:Credit');
            $entities = $repo->search(array('numDos' => $numDos));
        
            if (count($entities) == 1) {
                return $this->redirect(
                    $this->generateUrl('editique_souscription_edit', array('id' => $entities[0]->getId()))
                );
            }
        }
        
        return $this->render('EditiqueCreditBundle:Souscription:index.html.twig', array(
            'numDos' => $numDos,
            'entities' => $entities
        ));
    }
    
    public function editAction($id)
    {
        $manager = $this->get('editique.creditManager');
        $em = $this->getDoctrine()->getManager();
        $em2 = $this->getDoctrine()->getManager('bfi2');
        
        $souscription = $em->getRepository('EditiqueMasterBundle:Souscription')->findOneByIdPret($id);
        $pret = $em2->getRepository('EditiqueCreditBundle:Credit')->find($id);
        
        $idClient = $manager->getEmprunteurWithCredit($pret->getNumDos(), $pret->getNumPret());
        
        if (!$idClient) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Une erreur est survenue. Vérifiez le données du crédit sur SAB puis rééssayer.' .
                ' Si le problème persiste, contactez le SI Banque.'
            );
            
            return $this->redirect($this->generateUrl('editique_souscription_credit'));
        }
        
        $plan = $manager->getPlanWithCredit($pret->getNumDos(), $pret->getNumPret());
        $creBis = $manager->getCreBisWithCredit($pret->getNumDos(), $pret->getNumPret());
        $client = $manager->getClientWithId($idClient);
        $client['id'] = $idClient;
        $naissance = $manager->getInfosNaissance($idClient);
        $dirigeants = $client['type'] == 'EI' ? null : $manager->getDirigeants($idClient);
        $type = $manager->getType($id);
        $jourPrelvmt = $manager->getJourPrelvmt($pret->getNumDos());
        $dateDecasmt = $manager->getDateDecasmt($pret->getNumDos(), $pret->getNumPret());
        
        return $this->render('EditiqueCreditBundle:Souscription:edit.html.twig', array(
            'pret' => $pret,
            'client' => $client,
            'naissance' => $naissance,
            'plan' => $plan,
            'creBis' => $creBis,
            'dirigeants' => $dirigeants,
            'souscription' => $souscription,
            'type' => $type,
            'jourPrelvmt' => $jourPrelvmt,
            'dtDec' => $dateDecasmt
        ));
    }
    
    public function doEditAction(Request $request, $id)
    {
        $manager = $this->get('editique.creditManager');
        $em = $this->getDoctrine()->getManager();
        $type = $manager->getType($id);
        
        if ($request->get('rcs') && $request->get('ass1') && $request->get('guarantee')) {
            // Formatage des garanties
            $textGuarantee = $request->get('guarantee');
            // Attention, le premier paramètre n'est pas un simple tiret !
            $textGuarantee2 = str_replace('–', '-', $textGuarantee);
            $textGuarantee3 = str_replace('’', '\'', $textGuarantee2);
            $textGuarantee4 = str_replace('«', '"', $textGuarantee3);
            $textGuarantee5 = str_replace('»', '"', $textGuarantee4);
            $textGuaranteeFinal = stripslashes($textGuarantee5);
            
            $textFormate = wordwrap($textGuaranteeFinal, 69, "\r\n");
            $garanties = explode("\r\n", $textFormate);
            
            if ($type != 'OUV') {
                // Formatage de l'objet
                $textObject = $request->get('obj_fin');
                // Attention, le premier paramètre n'est pas un simple tiret !
                $textObject2 = str_replace('–', '-', $textObject);
                $textObject3 = str_replace('’', '\'', $textObject2);
                $textObject4 = str_replace('«', '"', $textObject3);
                $textObject5 = str_replace('»', '"', $textObject4);
                $textObjectFinal = stripslashes($textObject5);

                $objectFormate = wordwrap($textObjectFinal, 69, "\r\n");
                $object = explode("\r\n", $objectFormate);

                if (count($object) > 8) {
                    $tooLong = true;
                } else {
                    $tooLong = false;
                }

                for ($i = 0; $i < count($object); $i++) {
                    $objectTroncated[] = $object[$i];
                }
            }
            
            $souscription = $em->getRepository('EditiqueMasterBundle:Souscription')->findOneByIdPret($id);
            
            // Le démon tourne continuellement. Et en cas de mise à jour de données, un résidu génant reste.
            // Le moyen de contournement trouvé est de supprimer la ligne en BDD pour la recréer de toutes parts
            // (plutôt que de mettre à jour la ligne). C'est bof mais ça marche :)
            if ($souscription) {
                $em->remove($souscription);
                $em->flush();
            }
            
            $souscription = new Souscription();
            
            $souscription
                ->setIdPret($id)
                ->setRcs($request->get('rcs'))
                ->setCapital($request->get('capital'))
                ->setVilleNaissance($request->get('villeNai'))
                ->setDescriptionEi($request->get('descEi'))
                ->setDirigeants($request->get('dirigeant'))
                ->setJourPre($request->get('jourPre'))
                ->setTypeCalcul($request->get('typeCalcul'))
                ->setNombreExemplaire($request->get('nbExe'))
                ->setAss1($request->get('ass1'))
                ->setAss2($request->get('ass2'))
                ->setAss3($request->get('ass3'))
                ->setAss4($request->get('ass4'))
                ->setAss5($request->get('ass5'))
                ->setGar1((bool)$request->get('gar1'))
                ->setGaranties($garanties)
                ->setFraisGar($request->get('fraisGar'))
            ;
            
            if ($type != 'OUV') {
                $souscription
                    ->setObjetFin($objectTroncated)
                    ->setDiffAmo($request->get('diffAmo'))
                    ->setDtDec($request->get('dtDec'))
                ;
            } else {
                $souscription
                    ->setObjetFin('null')
                    ->setComEng1($request->get('comeng1'))
                    ->setComEng2($request->get('comeng2'))
                ;
            }
            
            $em->persist($souscription);
            $em->flush();
            
            if ($type != 'OUV') {
                if ($tooLong) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'Attention, l\'objet de financement a été tronqué !'
                    );
                }
            }
            
            if ($request->get('generate') !== null) {
                $action = new Action();

                $action
                    ->setType('EDITIQUE')
                    ->setModule('SOUSCRIPTION_CREDIT')
                    ->setNumCpt($id);

                $em->persist($action);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Données enregistrées et Editique généré avec succès.'
                );
            } else {
                $this->get('session')->getFlashBag()->add('success', 'Données enregistrées avec succès.');
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Le formulaire saisi comporte des erreurs. Veuillez réessayer.'
            );
        }
        
        return $this->redirect($this->generateUrl('editique_souscription_credit'));
    }
}
