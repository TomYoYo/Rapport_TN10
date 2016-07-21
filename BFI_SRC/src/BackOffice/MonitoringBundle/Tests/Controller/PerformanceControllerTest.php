<?php

namespace BackOffice\CleanBundle\Tests\Controller;

use FrontOffice\MainBundle\Tests\Controller\MainControllerTest;

class PerformanceControllerTest extends MainControllerTest
{
    public function testIndex()
    {
        $this->gotoRoute('back_office_monitoring_performance');
        //$this->debug(false);
        $this->checkPresence('Performance SAB - JOUR');
    }
}
