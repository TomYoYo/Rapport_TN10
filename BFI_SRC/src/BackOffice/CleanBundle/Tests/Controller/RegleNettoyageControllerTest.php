<?php

namespace BackOffice\CleanBundle\Tests\Controller;

use FrontOffice\MainBundle\Tests\Controller\MainControllerTest;

class RegleNettoyageControllerTest extends MainControllerTest
{
    public function testIndex()
    {
        $this->gotoURL('backoffice/clean/');
        //$this->debug(false);
        $this->checkPresence('Rapports de nettoyage');
    }
}
