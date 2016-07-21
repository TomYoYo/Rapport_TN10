<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackOffice\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Controller\ResettingController as BaseController;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends BaseController
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        return $this->container->get('templating')->renderResponse(
            'BackOfficeUserBundle:Resetting:request.html.' . $this->getEngine()
        );
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Le nom d\'utilisateur saisi est incorrect ou non reconnu.'
            );
            return $this->container->get('templating')->renderResponse(
                'FOSUserBundle:Resetting:request.html.' . $this->getEngine()
            );
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Un nouveau mot de passe a déjà été demandé pour cet utilisateur dans les dernières 24 heures.'
            );
            return $this->container->get('templating')->renderResponse(
                'FOSUserBundle:Resetting:request.html.' . $this->getEngine()
            );
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        //$this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        // Envoi mail à l'utilisateur
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

        mail(
            $user->getEmail(),
            '=?utf-8?B?'.base64_encode('Réinitialisation de votre mot de passe').'?=',
            //'R&eacute;initialisation de votre mot de passe',
            $this->container->get('templating')->render(
                'FrontOfficeMainBundle:Mail:mail.html.twig',
                array(
                    'parts' => array(
                        array(
                            'title' => 'Réinitialisation de votre mot de passe',
                            'content' => $this->container->get('templating')->render(
                                'BackOfficeUserBundle:Resetting:mail_reset_password.html.twig',
                                array(
                                    'username' => $user->getPrenom()." ".$user->getNom(),
                                    'confirmationUrl' => $this->container->get('router')->generate(
                                        'fos_user_resetting_reset',
                                        array('token' => $user->getConfirmationToken()),
                                        true
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            $headers
        );
            
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Un e-mail a été envoyé à l\'adresse ' . $user->getEmail() . '. '
            . 'Il contient un lien sur lequel il vous faudra cliquer afin de réinitialiser votre mot de passe.'
        );
        return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Resetting:checkEmail.html.' . $this->getEngine(),
            array(
                'email' => $email,
            )
        );
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token)
            );
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $this->container->get('session')->getFlashBag()->add(
                        'success',
                        'Votre mot de passe a été ré-initialisé avec succès.'
                    );
                    $url = $this->container->get('router')->generate('front_office_main_homepage');
                    $response = new RedirectResponse($url);
                }

                $lm = $this->container->get('backoffice_monitoring.logManager');
                $lm->addInfo(
                    $user->getUsername().' a réinitialisé son mot de passe.',
                    'BackOffice > User',
                    'Reset du mot de passe'
                );
                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Resetting:reset.html.' . $this->getEngine(),
            array(
                'token' => $token,
                'form'  => $form->createView(),
            )
        );
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
}
