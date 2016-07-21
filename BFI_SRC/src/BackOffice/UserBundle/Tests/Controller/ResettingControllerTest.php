<?php

namespace BackOffice\UserBundle\Tests\Controller;

use atoum\AtoumBundle\Test\Controller\ControllerTest;

class ResettingController extends ControllerTest
{
    public function testGet()
    {
        $this
            ->request(array('debug' => true))
                ->GET('/resetting/request')
                    ->hasStatus(200)
                    ->hasCharset('UTF-8')
                    ->crawler
                        ->hasElement('h1')
                            ->withContent('Ré-initialisation du mot de passe')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'text')
                            ->withAttribute('name', 'username')
                        ->end()
                        ->hasElement('input')
                            ->withAttribute('type', 'submit')
                            ->withAttribute('name', 'submit')
                        ->end()
        ;
        
    }
}
