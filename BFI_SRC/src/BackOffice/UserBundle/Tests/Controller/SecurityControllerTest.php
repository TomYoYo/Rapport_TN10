<?php

namespace BackOffice\UserBundle\Tests\Controller;

use atoum\AtoumBundle\Test\Controller\ControllerTest;

class SecurityController extends ControllerTest
{
    public function testGet()
    {
        $this
            ->request(array('debug' => true))
                ->GET('/login')
                    ->hasStatus(200)
                    ->hasCharset('UTF-8')
                    ->crawler
                        ->hasElement('h1')
                            ->withContent('Connexion')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'text')
                            ->withAttribute('name', '_username')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'password')
                            ->withAttribute('name', '_password')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'hidden')
                            ->withAttribute('name', '_csrf_token')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'submit')
                            ->withAttribute('name', '_submit')
                        ->end()
        ;
        
    }
}
