<?php

namespace BackOffice\ConnexionBundle\Manager;

use BackOffice\CleanBundle\Entity\RegleNettoyage;
use BackOffice\ParserBundle\Manager\ParserManager;

class SFTPManager
{
    public $hostname = '';
    public $username = '';
    public $port = 22;
    public $conn_sftp = false;
    public $public_key_url = '';
    public $private_key_url = '';
    public $dirUnijob = '';
    public $buffer_size = 1024;
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
        if (!$this->conn_sftp = @ssh2_connect($this->hostname, $this->port)) {
            $this->addError('Connexion SFTP à ' . $this->hostname . ' échouée.', 'Connexion SFTP');
            return false;
        }

        if (@ssh2_auth_pubkey_file(
            $this->conn_sftp,
            $this->username,
            $this->public_key_url,
            $this->private_key_url
        ) !== false) {
            $this->addSuccess("Connexion SFTP par clé à " . $this->hostname . " réussie.", 'Connexion SFTP');
            $this->isConnected = true;
            return true;
        } else {
            $this->addError("Connexion SFTP par clé à " . $this->hostname . " échouée.", 'Connexion SFTP');
            $this->isConnected = false;
            return false;
        }
    }

    public function logout()
    {
        $this->conn_sftp = null;
        $this->isConnected = false;
    }

    public function isConnected()
    {
        return $this->conn_sftp != null and $this->isConnected === true;
    }

    /**
     * Change les droits d'un fichier distant
     * @param string $file
     * @return boolean
     */
    public function chmod($file, $chmod = '644')
    {
        if (!$this->isConnected()) {
            return false;
        }

        @ssh2_exec($this->conn_sftp, "chmod $chmod $file");

        $this->addSuccess('Changement de droit OK sur '.$file, 'CHMOD SFTP');

        return true;
    }

    /**
     * Upload a file to the server
     * @param string $locpath chemin vers le fichier local
     * @param string $rempath chemin vers le fichier distant
     * @param string $chmod chemin vers le fichier distant     *
     * @return bool
     */
    public function upload($locpath, $rempath, $chmod = '664')
    {
        if (!$this->isConnected()) {
            return false;
        }

        if (!file_exists($locpath)) {
            $this->addError('Fichier demandé (' . $locpath . ') inexistant.', 'Upload SFTP');
            return false;
        }

        $stream = @fopen("ssh2.sftp://$this->conn_sftp$rempath", 'w');
        if ($stream === false) {
            $this->addError('Ouverture en ecriture vers ' . $rempath . ' impossible.', 'Upload SFTP');
            return false;
        }

        $data_to_send = @file_get_contents($locpath);
        @fwrite($stream, $data_to_send);

        $this->chmod($rempath, $chmod);

        @fclose($stream);
        $this->addSuccess('Upload effectué avec succès.', 'Upload SFTP');

        return true;
    }

    /**
     * Download a file from the remote server
     * @param string $rempath chemin du fichier distant
     * @param string $locpath chemin du fichier local
     * @return bool
     */
    public function download($rempath, $locpath)
    {
        if (!$this->isConnected()) {
            return false;
        }

        // on vérifie que le fichier existe
        if ($this->fileExists($rempath) === false) {
            $this->addError('Download impossible car '.$rempath.' n\'existe pas.', 'Download SFTP');
            return false;
        }

        $stream = @fopen("ssh2.sftp://$this->conn_sftp$rempath", 'r');

        if ($stream === false) {
            $this->addError('Ouverture du fichier/dossier distant (' . $rempath . ') impossible.', 'Download SFTP');
            return false;
        }

        $contents = null;

        while (!feof($stream)) {
            $contents .= @fread($stream, $this->buffer_size);
        }

        @file_put_contents($locpath, $contents);
        @fclose($stream);
        $this->addSuccess('Download effectué avec succès.', 'Download SFTP');

        return true;
    }

    /**
     * Deplace un fichier vers un nouveau repertoire
     * @param string $old_file
     * @param string $repDest
     * @return boolean
     */
    public function mv($old_file, $repDest)
    {
        if (!$this->isConnected()) {
            return false;
        }

        @ssh2_exec($this->conn_sftp, "mv $old_file $repDest");

        // on vérifie que le fichier est à l'emplacement voulu
        $new_file = $repDest.  basename($old_file);
        if ($this->fileExists($new_file) === false) {
            $this->addError('Déplacement échoué : '.$new_file.' pas là', 'Déplacement SFTP');
            return false;
        }
/*
        // on vérifie que le old file n'est plus là
        if ($this->fileExists($old_file) === true) {
            $this->addError('Déplacement échoué : '.$old_file.' toujours là', 'Déplacement SFTP');
            return false;
        }
*/
        $this->addSuccess('Déplacement effectué avec succès', 'Déplacement SFTP');

        return true;
    }

    /**
     * Renomme un fichier
     * @param string $old_file
     * @param string $new_name
     * @return boolean
     */
    public function rename($old_file, $new_name)
    {
        if (!$this->isConnected()) {
            return false;
        }

        $new_file = dirname($old_file) . '/' . $new_name;

        @ssh2_exec($this->conn_sftp, "mv '$old_file' '$new_file'");

        // on vérifie que le fichier est à l'emplacement voulu
        if ($this->fileExists($new_file) === false) {
            $this->addError('Renommage échoué : '.$new_file.' pas là', 'Renommage SFTP');
            return false;
        }

        $this->addSuccess('Renommage effectué avec succès', 'Renommage SFTP');

        return true;
    }

    /**
     * Delete a file from the remote server
     * @param $rempath chemin distant
     * @return bool
     */
    public function delete($rempath)
    {
        if (!$this->isConnected()) {
            return false;
        }

        @ssh2_exec($this->conn_sftp, 'rm -f "'.$rempath.'"');

        // on vérifie que la suppression a bien eu lieu
        if ($this->fileExists($rempath) === true) {
            $this->addError('Suppression via SSH de '.$rempath.' impossible (pb de droit).', 'Delete SFTP');
            return false;
        }

        $this->addSuccess('Suppression via SSH de '.$rempath.' effectuée avec succès.', 'Delete SFTP');
        return true;
    }

    /**
     * Retourne la liste des fichiers d'un repertoire donné
     * @param type $path
     * @return array
     */
    public function listFiles($path = '.')
    {
        if (!$this->isConnected()) {
            return false;
        }

        $result = @ssh2_exec($this->conn_sftp, "ls $path");
        stream_set_blocking($result, true);
        $s = stream_get_contents($result);

        $res = array();
        if (strpos($s, "\n") !== false) {
            $res = explode("\n", $s);
        }

        @fclose($result);

        return $res;
    }

    /**
     * Retourne la liste des fichiers correspondant au masque fourni
     * @param type $path
     * @param type $m
     * @return type
     */
    public function fichiersMasques($path = '.', $m = "/\.txt$/")
    {
        $res = array();
        $files = $this->listFiles($path);
        if ($files !== false) {
            foreach ($files as $f) {
                if (preg_match($m, $f)) {
                    $res [] = $f;
                }
            }
        }

        return $res;
    }

    /**
     * Retourne la liste des fichiers correspondant au masque fourni avec
     * récursivité dans les sous-dossiers
     * @param type $path chemin premiere boucle
     * @param type $subPath chemin seconde boucle
     * @param type $m la masque des sous dossier recherchés
     * @return array
     */
    public function fichiersSousDossiersMasques($path = '.', $subPath = '.', $m = "/\.txt$/")
    {
        $res = array();
        $directories = $this->listFiles($path);
        $repertories = array();

        if ($directories !== false) {
            foreach ($directories as $directory) {
                $filesInDirectory[$path . $directory . $subPath] = $this->listFiles($path . $directory . $subPath);
                $repertories = array_merge($repertories, $filesInDirectory);
            }

            foreach ($repertories as $path => $files) {
                foreach ($files as $f) {
                    if (preg_match($m, $f)) {
                        $res [$path][] = $f;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Renvoi si le fichier distant ciblé par $rempath existe
     * @param string $rempath chemin vers le fichier recherche
     * @return boolean true si le fichier est trouvé
     */
    public function fileExists($rempath)
    {
        $result = @ssh2_exec($this->conn_sftp, '[ -f "'.$rempath.'" ] && echo "Found" || echo "Not found"');
        if ($result !== false) {
            stream_set_blocking($result, true);
            $s = stream_get_contents($result);
            @fclose($result);

            if (trim($s) === 'Not found') {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getContent($path)
    {
        if (!$this->isConnected()) {
            return false;
        }

        // on vérifie que le fichier existe
        if ($this->fileExists($path) === false) {
            return 'INEXISTANT';
        }

        $content = @file("ssh2.sftp://$this->conn_sftp$path");

        if ($content === false) {
            $this->addError('Ouverture du fichier/dossier distant (' . $path . ') impossible.', 'Download SFTP');
            return false;
        }

        return $content;
    }

    public function checkSousDossier(RegleNettoyage $regle, $dan)
    {
        $sousDossier = preg_replace('#[^./0-9a-z]+#i', '', $regle->getSousDossier());

        if ($sousDossier == '') {
            return true;
        }

        $dir = $dan[$regle->getOriginDir()];
        $path = $dir.'/'.$sousDossier;

        $cmd = 'if [ -d "'.$path.'" ]; then echo "ok"; else echo "ko"; fi';
        echo 'Je lance '.$cmd."\n";
        $stream = @ssh2_exec($this->conn_sftp, $cmd);
        stream_set_blocking($stream, true);
        $res = stream_get_contents($stream);
        echo 'Res : '.$res."\n";

        return trim($res) == 'ok';
    }

    /**
     * Execute la regle de nettoyage :
     *      Trouve
     *      Deplace ds dossier mensuel
     *      Compresse
     * @param RegleNettoyage $regle
     * @param array $dan dossier à nettoyer
     * @return array (nb fichier nettoyes, array(chemin des archive crees))
     */
    public function clean(RegleNettoyage $regle, $dan)
    {
        $res = false;
        if (!$this->isConnected() or !isset($dan[$regle->getOriginDir()])) {
            return $res;
        }

        $ageNbJour = $regle->getAgeNbJour();
        $dir = $dan[$regle->getOriginDir()];
        $path = $dir.'/'.$regle->getSousDossier();

        // nb fichier concerne
        $cmd = "find $path -maxdepth 1 -type f -mtime +$ageNbJour -name '*' -exec ls {} \; | wc -l";
        $stream = @ssh2_exec($this->conn_sftp, $cmd);
        stream_set_blocking($stream, true);
        $nbFichiersConcernes = stream_get_contents($stream);

        /**
         * Voici la commande pour ranger les fichiers dans les dossiers de leur mois :
         *  find /sab/sab139u/prints/PRT01/ -maxdepth 1 -type f -mtime +200 -name '*' -exec ls --full-time {} \;
         * | awk '{ if (NF > 5) print "if [ ! -d " substr($(NF-3),0,7) " ]; then mkdir " substr($(NF-3),0,7) "; fi;
         * mv "$(NF) " " substr($(NF-3),0,7)";"; }'
         * | sh
         */
        $dir = $path."dir4clean\"substr($(NF-3),0,7) \"";
        $cmd = "find $path -maxdepth 1 -type f -mtime +$ageNbJour -name '*' -exec ls --full-time {} \;";
        $cmd .= " | awk '{ if (NF > 5) print \"if [ ! -d $dir ]; then mkdir $dir; fi; mv \"$(NF) \" $dir;\"; }'";
        $cmd .= " | sh";
        //echo 'Je lance : '.$cmd."\n";

        @ssh2_exec($this->conn_sftp, $cmd);

        echo 'J\'ai fini de ranger'."\n";

        $cmd = 'find ' . $path . ' -type d -name \'dir4clean*\' -exec basename {} \; | awk \'{print "tar -czf ' .
            $path . '"$NF"-' . date('ymd_His') . '.tar.gz ' . $path . '"$NF}\' | sh';
        //echo 'Je lance : '.$cmd."\n";
        @ssh2_exec($this->conn_sftp, $cmd);
        echo 'J\'ai fini de compresser'."\n";

        $cmd = 'find ' . $path . ' -type d -name \'dir4clean*\' -exec basename {} \; | awk \'{print "rm -rf ' .
            $path . '"$NF}\' | sh';
        //echo 'Je lance : '.$cmd."\n";
        @ssh2_exec($this->conn_sftp, $cmd);
        echo 'J\'ai fini de nettoyer'."\n";

        // archives crees
        $cmd = "find $path -maxdepth 1 -type f -mmin -120 -name 'dir4clean*' -exec ls {} \;";
        $stream = @ssh2_exec($this->conn_sftp, $cmd);
        stream_set_blocking($stream, true);
        $archivesCrees = stream_get_contents($stream);

        return array($nbFichiersConcernes, explode("\n", $archivesCrees));
    }

    public function getPerfUnijob($job = 'LINUX')
    {
        $cmd="
            source $this->dirUnijob/data/unienv.ksh > /dev/null;
            sudo $this->dirUnijob/app/bin/unilst RUN | grep $job | tail -n 1 | awk '{ print $(NF-3) \" \" $(NF-2) \" \" $(NF-1) \" \" $(NF)}'
        ";

        $stream = ssh2_exec($this->conn_sftp, $cmd);
        stream_set_blocking($stream, true);
        $res = stream_get_contents($stream);
        echo trim($res);
        //var_dump(trim($res));die;

        $tmp = explode('|', trim($res));

        if (count($tmp) < 2) {
            return false;
        }

        $debut  = ParserManager::getDateTimeFromUniJobReport($tmp[1]);
        $fin    = ParserManager::getDateTimeFromUniJobReport($tmp[2]);

        if ($debut === false || $fin === false) {
            return 1;
        }

        $interval = date_diff($fin, $debut);

        $nbSecondes =
            (int)$interval->format('%s') +
            60 * (int)$interval->format('%i') +
            3600 * (int)$interval->format('%h')
            ;

        return $nbSecondes;
    }

    // a voir si cela peut
    public function downloadArchive($remoteArchive, $localArchive)
    {
        return ssh2_scp_recv($this->conn_sftp, $remoteArchive, $localArchive);
    }
}
