<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\ConnexionBundle\Manager\SFTPManager;

/**
 * Description of releveManager
 *
 * @author d.briand
 */
class DwmSFTPManager extends SFTPManager
{
    public function __construct(
        $hostname = '',
        $username = '',
        $public_key_url = '',
        $private_key_url = ''
    ) {
        $config = array(
            'hostname'          => $hostname,
            'username'          => $username,
            'public_key_url'    => $public_key_url,
            'private_key_url'   => $private_key_url
        );

        parent::__construct($config);
    }
}
