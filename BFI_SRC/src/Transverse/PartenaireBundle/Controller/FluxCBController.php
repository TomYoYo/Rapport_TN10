<?php

namespace Transverse\PartenaireBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Transverse\PartenaireBundle\Entity\Bic;
use Transverse\PartenaireBundle\Entity\Mail;
use Transverse\PartenaireBundle\Entity\PainEnErreur;

class FluxCBController extends Controller
{
    public function indexAction()
    {
        return $this->render('TransversePartenaireBundle:FluxCB:index.html.twig');
    }

    public function administrationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mails = $em->getRepository('TransversePartenaireBundle:Mail')->findAll();

        $mail   = $request->request->get('mail');
        if ($mail != null) {

            $leMail = new Mail();
            $leMail->setMail($mail);

            $em->persist($leMail);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'L\'adresse mail a bien été enregistré en base.');
            return $this->redirect(
                $this->generateUrl(
                    'transverse_partenaire_fluxCB_administration'
                )
            );
        }

        return $this->render('TransversePartenaireBundle:FluxCB:Administration/ControlesBic/index.html.twig', array(
            'req' => $request,
            'mails' => $mails,
        ));

    }


    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mail = $em->getRepository('TransversePartenaireBundle:Mail')->find($id);
        $mails = $em->getRepository('TransversePartenaireBundle:Mail')->findAll();

        if (null === $mail) {
            throw new NotFoundHttpException("Le mail numéro " . $id . " n'existe pas.");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $em->remove($mail);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le mail a bien été supprimé.");

            return $this->redirect($this->generateUrl('transverse_partenaire_fluxCB_administration'));
        }

        //si GET, on affiche une page de confirmation
        return $this->render('TransversePartenaireBundle:FluxCB:Administration/ControlesBic/delete.html.twig', array(
            'mail' => $mail,
        ));
    }

    public function consultationAction()
    {
        $painBfiRepertory  = $this->container->getParameter('dirPains');

        $em = $this->getDoctrine()->getManager();
        $PainEnErreurs = $em->getRepository('TransversePartenaireBundle:PainEnErreur')->findAll();

        return $this->render('TransversePartenaireBundle:FluxCB:Consultation/ControlesBic/index.html.twig', array(
            'PainEnErreurs' => $PainEnErreurs,
            'urlBFIPain' => $painBfiRepertory,
        ));
    }


    public function painshowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pain = $em->getRepository('TransversePartenaireBundle:PainEnErreur')->find($id);

        $painBfiRepertory  = $this->container->getParameter('dirPains');

        if (null === $pain) {
            throw new NotFoundHttpException("Le fichier PAIN n'existe pas.");
        }
        $filename = basename($pain->getUrlPain());

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($painBfiRepertory.$filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', filesize($painBfiRepertory.$filename));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($painBfiRepertory.$filename));
        return $response;
    }

    public function testAction()
    {
        $user = $this->container->getParameter('sabCore.user');
        $server = $this->container->getParameter('sabCore.server');
        //$clePrivee = $this->container->getParameter('sabCore.clePrivee');
        //$clePublic = $this->container->getParameter('sabCore.clePublic');
        $clePrivee = $this->container->getParameter('sabCore.clePriveeApache');
        $clePublic =$this->container->getParameter('sabCore.clePublicApache');

        $painRepertory  = $this->container->getParameter('sabCore.PainFiles');
        $painBfiRepertory  = $this->container->getParameter('dirPains');


        $session  = ssh2_connect($server, 22, array('hostkey'=>'ssh-rsa,ssh-dss'));

        if (ssh2_auth_pubkey_file(
            $session,
            $user,
            "/.".$clePublic,
            "/.".$clePrivee,
            null
        )) {
            var_dump('connexion ok');
        } else {
            var_dump('connexion K.O');
        }

        $stream = ssh2_exec($session, 'find /.'.$painRepertory.' -maxdepth 1 -name *.xml');
        stream_set_blocking($stream, true);
        $FilesName = stream_get_contents($stream);
        fclose($stream);

        $filesTmp = explode("\n", $FilesName);

        $PainMovedOnBFI = array();

        foreach ($filesTmp as $keyfilesTmp => $valuefilesTmp) {

            if ($valuefilesTmp != null) {
                ssh2_scp_recv($session, $valuefilesTmp, $painBfiRepertory . basename($valuefilesTmp));
                $PainMovedOnBFI[] = basename($valuefilesTmp);
            }
        }

        $em = $this->getDoctrine()->getManager();
        //$bics correspond aux BIC stockés dans SAB, c'est la référence qu'on utilise pour vérifier les BICS des fichiers PAIN
        $bics = $em->getRepository('TransversePartenaireBundle:Bic')->findBic();
        //On ne garde que la valeur et on nettoie le nom de la clé
        $onlyBic = array_column($bics, 'bic');
        //on supprime les doublons pour gagner de la place sur le tableau
        $onlyBic = array_unique($onlyBic);

        foreach ($PainMovedOnBFI as $key => $lePain) {
            $fichier = $painBfiRepertory.$lePain;
            $xml = simplexml_load_file($fichier);
            //On récupère le BIC pour chaque balises DrctDbtTxInf
            //les balises BIC sont au 6eme niveau $xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->DbtrAgt->FinInstnId->BIC
            //On compte le nombre de balises
            $p_cnt = count($xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf);
            $BicInError = array();

            for ($i = 0; $i < $p_cnt; $i++) {
                $param = $xml->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf[$i];
                //on vérifie si le BIC ($param->DbtrAgt->FinInstnId->BIC) est dans la base de vérification $bics
                if (!in_array($param->DbtrAgt->FinInstnId->BIC, $onlyBic)) {
                    $BicInError[] = $param->DbtrAgt->FinInstnId->BIC;
                }

            }
            var_dump($BicInError);
            if (!empty($BicInError)) {
                //On déplace le fichier dans le répertoire erreur
                $stream = ssh2_exec($session, 'mv '.$painRepertory.$lePain.' '.$painRepertory.'/ERROR/');
                //On stock l'URL bfi dans la bdd bfi
                $PainEnErreur = new PainEnErreur();
                $PainEnErreur->setUrlPain($lePain);
                $arrayBicInError = json_decode(json_encode($BicInError), true);
                $PainEnErreur->setBicsEnErreur($arrayBicInError);
                $em->persist($PainEnErreur);
                $em->flush();
            }
            unset( $arrayBicInError );
            unset( $BicInError );
        }


    }
}
