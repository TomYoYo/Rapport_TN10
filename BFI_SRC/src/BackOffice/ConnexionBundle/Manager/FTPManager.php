<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\CleanBundle\Entity\RegleNettoyage;

class FTPManager
{
    public $hostname = '';
    public $port = '21';
    public $username = '';
    public $password = '';
    public $conn_ftp = false;
    public $logManager;
    public $isConnected = false;

    public function setLogManager($lm)
    {
        $this->logManager = $lm;
    }

    public function __construct($config = array())
    {
        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }

    public function __destruct()
    {
        $this->logout();
    }

    public function addError($msg, $action)
    {
        $this->logManager->addError($msg, 'BackOffice > Connexion', $action);
    }

    public function addSuccess($msg, $action)
    {
        $this->logManager->addSuccess($msg, 'BackOffice > Connexion', $action);
    }

    public function login()
    {
        if (!$this->conn_ftp = @ftp_connect($this->hostname, $this->port)) {
            $this->addError('Connexion FTP à '.$this->hostname.' échouée.', 'Connexion FTP');
            $this->isConnected = false;
            return false;
        }

        if (@ftp_login($this->conn_ftp, $this->username, $this->password)) {
            $this->addSuccess("Connexion FTP basique à " . $this->hostname . " réussie.", 'Connexion FTP');
            $this->isConnected = true;
            return true;
        } else {
            $this->addError("Connexion FTP basique à " . $this->hostname . " échouée.", 'Connexion FTP');
            $this->isConnected = false;
            return false;
        }
    }

    public function logout()
    {
        if ($this->isConnected) {
            ftp_close($this->conn_ftp);
            $this->isConnected = false;
        }
    }

    /**
     * Upload a file to the remote server
     * @param $locpath chemin local
     * @param $rempath chemin distant
     * @return bool
     */
    public function upload($locpath, $rempath)
    {
        if (!$this->isConnected && !$this->login()) {
            return false;
        }

        // passage en mode passif
        ftp_pasv($this->conn_ftp, true);

        if (@ftp_put($this->conn_ftp, $rempath, $locpath, FTP_BINARY) === false) {
            $this->addError('ftp_put de '.$locpath.' vers '.$rempath.' impossible.', 'Upload FTP');
            return false;
        }

        $this->addSuccess('Upload vers '.$rempath.' effectué avec succès.', 'Upload FTP');
        return true;
    }

    /**
     * Download a file from the remote server
     * @param $rempath chemin distant
     * @param $locpath chemin local
     * @return bool
     */
    public function download($rempath, $locpath)
    {
        if (!$this->isConnected) {
            return false;
        }

        if (@ftp_get($this->conn_ftp, $locpath, $rempath, FTP_BINARY, 0) === false) {
            $this->addError('ftp_get de '.$rempath.' impossible', 'Download FTP');
            return false;
        }

        $this->addSuccess('Download effectué avec succès.', 'Download FTP');
        return true;
    }

    /**
     * Delete a file from the remote server
     * @param $rempath chemin distant
     * @return bool
     */
    public function delete($rempath)
    {
        if (!$this->isConnected) {
            return false;
        }

        if (@ftp_delete($this->conn_ftp, $rempath) === false) {
            $this->addError('ftp_delete de '.$rempath.' impossible.', 'Delete FTP');
            return false;
        }

        $this->addSuccess('ftp_delete de '.$rempath.' effectué avec succès.', 'Delete FTP');
        return true;
    }
}
