<?php

namespace BackOffice\UserBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * Custom login listener.
 */
class AuthenticationListener
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $context;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Constructor
     *
     * @param SecurityContext $context
     * @param Doctrine        $doctrine
     */
    public function __construct(SecurityContext $context, Doctrine $doctrine, $lm)
    {
        $this->context = $context;
        $this->em = $doctrine->getManager();
        $this->logManager = $lm;
    }

    /**
     * Do the magic.
     *
     * @param  Event $event
     */
    public function onSecurityInteractiveLogin(Event $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->logManager->addInfo(
            'Connexion à l\'application de ' . $user->getUsername(),
            'BackOffice > User',
            'Authentification utilisateur'
        );
    }
}
