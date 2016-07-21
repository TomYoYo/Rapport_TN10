<?php

namespace BackOffice\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BackOfficeUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
