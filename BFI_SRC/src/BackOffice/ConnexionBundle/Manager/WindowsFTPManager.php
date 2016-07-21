<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\ConnexionBundle\Manager\FTPManager;

class WindowsFTPManager extends FTPManager
{
    public $repBqClient;
    public $maskNonClasse;
    public $dirDepotPDF;
    public $dirDepotPDFPerdu;

    public function __construct(
        $hostname = '',
        $username = '',
        $password = '',
        $repBqClient = '',
        $maskNonClasse = '',
        $dirDepotPDF = '',
        $dirDepotPDFPerdu = '',
        $dirDepotReleve = '',
        $dirDepotEsab = '',
        $dirDepotImpaye = '',
        $dirDepotCaution = ''
    ) {
        $this->repBqClient = $repBqClient;
        $this->maskNonClasse = $maskNonClasse;
        $this->dirDepotPDF = $dirDepotPDF;
        $this->dirDepotPDFPerdu = $dirDepotPDFPerdu;
        $this->dirDepotReleve = $dirDepotReleve;
        $this->dirDepotEsab = $dirDepotEsab;
        $this->dirDepotImpaye = $dirDepotImpaye;
        $this->dirDepotCaution = $dirDepotCaution;

        $config = array(
            'hostname' => $hostname,
            'username' => $username,
            'password' => $password
        );

        parent::__construct($config);
    }

    /**
     * Copie le fichier $file dans le dossier client du serveur de fichier
     * @param string $idClient
     * @param string $file
     * @return boolean true si la copie s'est bien passée
     */
    public function copieRepClient($idClient, $file)
    {
        $remoteDossier = '';

        // Récupération du contenu d'un dossier
        $dossiers = ftp_nlist($this->conn_ftp, $this->repBqClient);
        if (!$dossiers) {
            return false;
        }
        
        // on va chercher les dossiers distants destinataire
        foreach ($dossiers as $d) {
            // le dossier client
            if (strpos($d, $idClient) === 0) { // apparait au debut
                $remoteDossier = $d . '/' . $this->dirDepotPDF;
                break;
            } elseif (strpos($d, $this->maskNonClasse) === 0) { // on repere le dossier poubelle
                $remoteDossier = $d . '/' . $this->dirDepotPDFPerdu;
            }
        }

        // si on a identifié un dossier de destination on copie dedans
        if ($remoteDossier !== '') {
            $fileName = basename($file);
            $remotePath = $this->repBqClient . $remoteDossier . $fileName;
            if ($this->upload($file, $remotePath) === true) {
                $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' effectué avec succès';
                $this->addSuccess($msg, 'Dépot FTP sur serveur fichier');
                return true;
            } else {
                $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' impossible';
                $this->addError($msg, 'Dépot FTP sur serveur fichier');
            }
        }
        
        $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' impossible (répertoire introuvable)';
        $this->addError($msg, 'Dépot FTP sur serveur fichier');

        return false;
    }
    
    /**
     * Copie le fichier $file dans le dossier DSI du serveur de fichier
     * @param string $type
     * @param string $file
     * @return boolean true si la copie s'est bien passée
     */
    public function copieDSI($file, $type)
    {
        switch ($type) {
            case 'releve':
                $remoteDossier = $this->dirDepotReleve;
                break;
            case 'esab':
                $remoteDossier = $this->dirDepotEsab;
                break;
            case 'impayes':
                $remoteDossier = $this->dirDepotImpaye;
                break;
            case 'cautions':
                $remoteDossier = $this->dirDepotCaution;
                break;
        }

        // si on a identifié un dossier de destination on copie dedans
        if ($remoteDossier !== '') {
            $fileName = basename($file);
            $remotePath = $remoteDossier . $fileName;
            if ($this->upload($file, $remotePath) === true) {
                $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' effectué avec succès';
                $this->addSuccess($msg, 'Dépot FTP sur serveur fichier');
                return true;
            } else {
                $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' impossible';
                $this->addError($msg, 'Dépot FTP sur serveur fichier');
            }
        }
        
        $msg = 'Dépot sur serveur de fichier de  ' . $remotePath.' impossible (répertoire introuvable)';
        $this->addError($msg, 'Dépot FTP sur serveur fichier');

        return false;
    }
}
