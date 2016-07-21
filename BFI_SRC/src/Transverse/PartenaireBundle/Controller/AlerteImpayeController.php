<?php

namespace Transverse\PartenaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Transverse\PartenaireBundle\Entity\MailOI25;
use Transverse\PartenaireBundle\Entity\Oi2504;
use Transverse\PartenaireBundle\Entity\Oi250109;


class AlerteImpayeController extends Controller
{
    public function indexAction()
    {
        return $this->render('TransversePartenaireBundle:AlerteImpaye:index.html.twig');
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $Oi25 = $em->getRepository('TransversePartenaireBundle:Oi250109')->find($id);

        $OI25BfiRepertory  = $this->container->getParameter('dirOI25');

        if (null === $Oi25) {
            throw new NotFoundHttpException("Le fichier OI25 n'existe pas.");
        }
        $filename = basename($Oi25->getNomFichierUrl());

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($OI25BfiRepertory.$filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', filesize($OI25BfiRepertory.$filename));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($OI25BfiRepertory.$filename));
        return $response;
    }

public function consultationAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $data   = $request->request->get('search');

        if (isset($data['dateOi25'])) {
            $date_format_normal = $data['dateOi25'];

        } else {
            $date_format_normal = date('d/m/Y');
        }

        $Oi25s = $em->getRepository('TransversePartenaireBundle:Oi250109')->findAll();

        //on ajoute les decimales sur les montants
        foreach ($Oi25s as $Oi25) {
            $dateJourneeEchange = $Oi25->getDateJourneeEchange();
            $dateJourneeEchange = date('d/m/Y',strtotime($dateJourneeEchange));
            $Oi25->setDateJourneeEchange($dateJourneeEchange);

            $oi2504s = $Oi25->getoI2504s();
            foreach ($oi2504s as $oi2504) {
                $montant = $oi2504->getMontantImpute();
                if ($montant != null) {
                    $decimale = $oi2504->getNombreDecimalesDeviseImpute();
                    if ($decimale > 0) {
                        $pos = (16 - $decimale);
                        $montant = substr_replace($montant, ',', $pos, 0);
                    }
                    $var = ltrim($montant, '0');
                    $oi2504->setMontantImpute($var);
                }

                $montantAchatBrut =  $oi2504->getMontantAchatBrut();
                if ($montantAchatBrut != null) {
                    $nombreDecimalesDeviseAchat = $oi2504->getNombreDecimalesDeviseAchat();
                    if ($nombreDecimalesDeviseAchat > 0) {
                        $pos = (16 - $nombreDecimalesDeviseAchat);
                        $montantAchatBrut = substr_replace($montantAchatBrut, ',', $pos, 0);
                    }
                    $var = ltrim($montantAchatBrut, '0');
                    $oi2504->setMontantAchatBrut($var);
                }

                $montantAchatDeviseOrigine =  $oi2504->getMontantAchatDeviseOrigine();
                if ($montantAchatDeviseOrigine != null) {
                    $nombreDecimaleDeviseOrigine = $oi2504->getNombreDecimaleDeviseOrigine();
                    if ($nombreDecimaleDeviseOrigine > 0) {
                        $pos = (16 - $nombreDecimaleDeviseOrigine);
                        $montantAchatDeviseOrigine = substr_replace($montantAchatDeviseOrigine, ',', $pos, 0);
                    }
                    $var = ltrim($montantAchatDeviseOrigine, '0');
                    $oi2504->setMontantAchatDeviseOrigine($var);
                }

                $montantCompenseEuro =  $oi2504->getMontantCompenseEuro();
                if ($montantCompenseEuro != null) {
                    $nbDecimalesDeviseCompensation = $oi2504->getNbDecimalesDeviseCompensation();
                    if ($nbDecimalesDeviseCompensation > 0) {
                        $pos = (16 - $nbDecimalesDeviseCompensation);
                        $montantCompenseEuro = substr_replace($montantCompenseEuro, ',', $pos, 0);
                    }
                    $var = ltrim($montantCompenseEuro, '0');
                    $oi2504->setMontantCompenseEuro($var);
                }

                $montantCommissionsBanqueInt =  $oi2504->getMontantCommissionsBanqueInt();
                if ($montantCommissionsBanqueInt != null) {
                    $nbDecimalesDeviseCompensation2 = $oi2504->getNbDecimalesDeviseCompensation2();
                    if ($nbDecimalesDeviseCompensation2 > 0) {
                        $pos = (12 - $nbDecimalesDeviseCompensation2);
                        $montantCommissionsBanqueInt = substr_replace($montantCommissionsBanqueInt, ',', $pos, 0);
                    }
                    $var = ltrim($montantCommissionsBanqueInt, '0');
                    $oi2504->setMontantCommissionsBanqueInt($var);
                }


                $montantCommissionsInterchange =  $oi2504->getMontantCommissionsInterchange();
                if ($montantCommissionsInterchange != null) {
                    $nbDecimalesDeviseCompensation3 = $oi2504->getNbDecimalesDeviseCompensation3();
                    if ($nbDecimalesDeviseCompensation3 > 0) {
                        $pos = (12 - $nbDecimalesDeviseCompensation3);
                        $montantCommissionsInterchange = substr_replace($montantCommissionsInterchange, ',', $pos, 0);
                    }
                    $var = ltrim($montantCommissionsInterchange, '0');
                    $oi2504->setMontantCommissionsInterchange($var);
                }

            }
        }

        return $this->render('TransversePartenaireBundle:AlerteImpaye:Consultation/index.html.twig', array(
            'Oi25s' => $Oi25s,
            'date_format_normal' => $date_format_normal,
        ));

    }


    public function administrationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mails = $em->getRepository('TransversePartenaireBundle:MailOI25')->findAll();

        $mail   = $request->request->get('mail');
        if ($mail != null) {

            $leMail = new MailOI25();
            $leMail->setMail($mail);

            $em->persist($leMail);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'L\'adresse mail a bien été enregistré en base.');
            return $this->redirect(
                $this->generateUrl(
                    'transverse_partenaire_alerteImpaye_administration'
                )
            );
        }

        return $this->render('TransversePartenaireBundle:AlerteImpaye:Administration/index.html.twig', array(
            'req' => $request,
            'mails' => $mails,
        ));

    }


    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mail = $em->getRepository('TransversePartenaireBundle:MailOI25')->find($id);
        $mails = $em->getRepository('TransversePartenaireBundle:MailOI25')->findAll();

        if (null === $mail) {
            throw new NotFoundHttpException("Le mail numéro " . $id . " n'existe pas.");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $em->remove($mail);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le mail a bien été supprimé.");

            return $this->redirect($this->generateUrl('transverse_partenaire_alerteImpaye_administration'));
        }

        //si GET, on affiche une page de confirmation
        return $this->render('TransversePartenaireBundle:AlerteImpaye:Administration/delete.html.twig', array(
            'mail' => $mail,
        ));
    }



}
