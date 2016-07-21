<?php

namespace BackOffice\MonitoringBundle\Manager;

use Doctrine\ORM\EntityManager;
use BackOffice\MonitoringBundle\Entity\Log;
use Symfony\Component\Security\Core\SecurityContext;
use BackOffice\MonitoringBundle\Entity\Mail;
use Symfony\Component\Templating\EngineInterface;

/**
 * Description of LogManager
 *
 * @author j.david
 */
class LogManager
{
    public $em;
    public $user;
    public $tpl;
    public $enable;
    public $mailenable;
    public $mailInterTime;
    public $processMode = false;
    public $tabErreurToSend = array();
    public $fatalErrorMet = false;

    public function __construct(
        EntityManager $entityManager,
        SecurityContext $context = null,
        EngineInterface $tpl = null,
        $enable = true,
        $mailenable = true,
        $mailInterTime = 15
    ) {
        $classToken = "Symfony\Component\Security\Core\Authentication\Token\AnonymousToken";
        $this->em = $entityManager;
        $this->tpl = $tpl;
        if ($context !== null
            && $context->getToken() !== null
            && get_class($context->getToken()) !== $classToken) {
            $this->user = $context->getToken()->getUser();
        } else {
            $this->user = null;
        }

        $this->enable = $enable;
        $this->mailenable = $mailenable;
        $this->mailInterTime = $mailInterTime;
    }

    public function setProcessMode($pm)
    {
        $this->processMode = (bool) $pm;
    }
    
    public function setFatalErrorMet($pm)
    {
        $this->fatalErrorMet = (bool) $pm;
    }

    public function getRepository()
    {
        return $this->em->getRepository('BackOfficeMonitoringBundle:Log');
    }

    public function persistAndFlush($entity)
    {
        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    public function addError($libelle, $module = '', $action = '', $notificationGroup = 'SYSTEM')
    {
        $this->fatalErrorMet = true;
        $this->addLog(Log::NIVEAU_ERREUR, $libelle, $module, $action, $notificationGroup);
    }

    public function addAlert($libelle, $module = '', $action = '')
    {
        $this->addLog(Log::NIVEAU_ALERT, $libelle, $module, $action);
    }

    public function addInfo($libelle, $module = '', $action = '')
    {
        $this->addLog(Log::NIVEAU_INFO, $libelle, $module, $action);
    }

    public function addSuccess($libelle, $module = '', $action = '')
    {
        $this->addLog(Log::NIVEAU_SUCCESS, $libelle, $module, $action);
    }

    public function addLog($niveau, $libelle, $module = '', $action = '', $notificationGroup = 'SYSTEM')
    {
        if ($this->enable === false) {
            return;
        }
        
        $log = new Log();
        $log
            ->setNiveau($niveau)
            ->setLibelle(substr($libelle, 0, 255))
            ->setModule($module)
            ->setAction($action)
            ->setUtilisateur($this->user);

        $this->persistAndFlush($log);

        if ($niveau == Log::NIVEAU_ERREUR && $this->mailenable === true) {
            if ($this->processMode === false) {
                $mail = new Mail();
                $mail
                    ->setLog($log)
                    ->setLibelleLog($log->getLibelle())
                    ->setActionLog($log->getAction())
                    ->setModuleLog($log->getModule());
                $this->persistAndFlush($mail);

                $this->sendMail($mail, $notificationGroup);
            } else {
                $this->tabErreurToSend[]=$log;
            }
        }
    }

    public function getDestMailErreur($notificationGroup)
    {
        $repoUser = $this->em->getRepository('BackOfficeUserBundle:Profil');
        $users = $repoUser->search(array('notification' => $notificationGroup));
        //$users = $repoUser->findByEmail('david.briand-exterieur@fiducial.net');

        $to = array();
        foreach ($users as $u) {
            $to []= $u->getPrenom().' '.$u->getNom() . '<' . $u->getEmail() . '>';
        }
        
        return implode(',', $to);
    }

    // Fonction qui vérifie la date de l'envoi pour éviter le flood
    private function sendMail($mailObject, $notificationGroup = 'SYSTEM')
    {
        $logs = null;

        // Vérification si un mail a déjà été envoyé
        $mails = $this->em->getRepository('BackOfficeMonitoringBundle:Mail')->searchLast(
            $mailObject->getActionLog(),
            $mailObject->getModuleLog(),
            $mailObject->getId()
        );
        foreach ($mails as $mail) {
            $logs []= $mail->getLog();
        }

        $lastSendingArray = $this->em->getRepository('BackOfficeMonitoringBundle:Mail')->lastSending(
            $mailObject->getActionLog(),
            $mailObject->getModuleLog(),
            $mailObject->getId()
        );

        if ($lastSendingArray['lastSending']) {
            $lastSending = new \Datetime($lastSendingArray['lastSending']);
        } else {
            $lastSending = null;
        }

        $dateTimeUnderMinutes = new \Datetime();
        $dateTimeUnderMinutes->modify('-'.(int)$this->mailInterTime.' minutes');

        if (!isset($lastSending) || $lastSending < $dateTimeUnderMinutes) {
            // Envoi mail à l'utilisateur
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

            mail(
                $this->getDestMailErreur($notificationGroup),
                "[BFI/Symfony] Erreur Systeme",
                $this->tpl->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                    'parts' => array(
                        array(
                            'title' => 'Erreur système',
                            'content' => $this->tpl->render(
                                'BackOfficeMonitoringBundle:Mailing:mail_log_error.html.twig',
                                array(
                                    'log' => $mailObject->getLog()
                                )
                            )
                        ),
                        array(
                            'title' => 'Antécédents',
                            'content' => $this->tpl->render(
                                'BackOfficeMonitoringBundle:Mailing:mail_log_error_bis.html.twig',
                                array(
                                    'logs' => $logs,
                                    'lastSending' => $lastSending
                                )
                            )
                        )
                    )
                )),
                $headers
            );
            $mailObject->setIsSended(true);
            foreach ($mails as $mail) {
                $mail->setIsSended(true);
                $this->em->persist($mail);
            }
            $this->em->persist($mailObject);
            $this->em->flush();
        }
    }

    public function sendMailProcessError($log, $tabLogCompt = array(), $titre = '', $notificationGroup = 'SYSTEM')
    {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

        mail(
            $this->getDestMailErreur($notificationGroup),
            "[BFI/Symfony] Erreur Systeme " . $titre,
            $this->tpl->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                'parts' => array(
                    array(
                        'title' => 'Erreur système ' . $titre,
                        'content' => $this->tpl->render(
                            'BackOfficeMonitoringBundle:Mailing:mail_log_error.html.twig',
                            array('log' => $log)
                        )
                    ),
                    array(
                        'title' => 'Détails',
                        'content' => $this->tpl->render(
                            'BackOfficeMonitoringBundle:Mailing:mail_log_error_detail.html.twig',
                            array('logs' => $tabLogCompt)
                        )
                    )
                )
            )),
            $headers
        );

        $mail = new Mail();
        $mail
            ->setLog($log)
            ->setLibelleLog($log->getLibelle())
            ->setActionLog($log->getAction())
            ->setModuleLog($log->getModule())
            ->setIsSended(true);

        $this->em->persist($mail);
        $this->em->flush();
    }
}
