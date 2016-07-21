<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\ConnexionBundle\Manager\FTPManager;

class AngersFTPManager extends FTPManager
{
    private $dirToPrint;
    private $dirGeneratedFiles;
    private $dirRawFiles;

    public function __construct(
        $hostname = '',
        $port = '21',
        $username = '',
        $password = '',
        $dirToPrint = '',
        $dirGeneratedFiles = '',
        $dirRawFiles = ''
    ) {
        $this->dirToPrint = $dirToPrint;
        $this->dirGeneratedFiles = $dirGeneratedFiles;
        $this->dirRawFiles = $dirRawFiles;

        $config = array(
            'hostname' => $hostname,
            'port' => $port,
            'username' => $username,
            'password' => $password
        );
        
        parent::__construct($config);
    }

    /**
     * Copie le fichier $file dans le dossier du serveur d'Angers
     * @param string $file
     * @param string $type (print)
     * @return boolean true si la copie s'est bien passée
     */
    public function copieRep($file, $type = '')
    {
        if ($file) {

            $remotePath = $this->dirRawFiles;
            if ($type == 'print') {
                $remotePath = $this->dirToPrint;
            }

            $fileName = basename($file);
            $remotePath .= '/' . $fileName;

            if ($this->upload($file, $remotePath) === true) {
                $msg = 'Dépot sur serveur d\'Angers de  ' . $remotePath.' effectué avec succès';
                $this->addSuccess($msg, 'Dépot FTP sur serveur Angers');
                return true;
            }
        }

        return false;
    }
}
