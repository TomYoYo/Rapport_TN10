<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\ConnexionBundle\Manager\SFTPManager;

/**
 * Description of releveManager
 *
 * @author jd labails
 */
class SabSFTPManager extends SFTPManager
{
    public function __construct(
        $hostname = '',
        $username = '',
        $public_key_url = '',
        $private_key_url = '',
        $dirUnijob = ''
    ) {
        $config = array(
            'hostname'          => $hostname,
            'username'          => $username,
            'public_key_url'    => $public_key_url,
            'private_key_url'   => $private_key_url,
            'dirUnijob'         => $dirUnijob
        );

        parent::__construct($config);
    }
}
