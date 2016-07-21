<?php

namespace BackOffice\FileBundle\Manager;

use BackOffice\ParserBundle\Manager\ParserManager;

/**
 * Description of FileManager
 *
 * @author j.david
 */
class FileManager
{
    public $logManager;

    public function __construct($lm)
    {
        $this->logManager = $lm;
    }

    /**
     * Ecrit un log 'BackOffice > File' selon le msg et le niveau
     * @param string $msg le libelle du log
     * @param string $action action du log
     * @param string $niveau niveau du log
     */
    public function addLog($msg, $action, $niveau = 'error')
    {
        switch ($niveau) {
            case 'error':
                $this->logManager->addError($msg, 'BackOffice > File', $action);
                break;
            case 'alert':
                $this->logManager->addAlert($msg, 'BackOffice > File', $action);
                break;
            case 'success':
                $this->logManager->addSuccess($msg, 'BackOffice > File', $action);
                break;
            case 'info':
                $this->logManager->addInfo($msg, 'BackOffice > File', $action);
                break;
        }
    }

    public function addLogLecture($e, $niveau = 'error')
    {
        $this->addLog($e, 'Lecture d\'un fichier interne', $niveau);
    }

    public function addLogEcriture($e, $niveau = 'error')
    {
        $this->addLog($e, 'Ecriture d\'un fichier interne', $niveau);
    }

    public function addLogDeplacement($e, $niveau = 'error')
    {
        $this->addLog($e, 'Déplacement d\'un fichier interne', $niveau);
    }

    /**
     * Lit un fichier et retourne son contenu
     * @param string $filePath chemin du fichier
     * @return string le contenu du fichier
     */
    public function lireFichier($filePath)
    {
        $fileContent = '';
        if (file_exists($filePath) === true) {
            $fileContent = file_get_contents($filePath);
            if (strlen($fileContent) > 0) {
                $this->addLogLecture('Fichier ('.$filePath.') lu avec succès.', 'success');
            } else {
                $this->addLogLecture('Fichier ('.$filePath.') vide.', 'alert');
            }
        } else {
            $this->addLogLecture('Fichier demandé ('.$filePath.') inexistant.');
        }
        return $fileContent;
    }

    /**
     * Ecrit un fichier dans le repertoire $dir et s'appelant $filename
     * et avec le contenu $content
     * @param string $dir chemin du dossier de destination
     * @param string $fileName nom du fichier à ecrire
     * @param string $content contenu du futur fichier
     * @return bool return true si pas d'erreur
     */
    public function ecrireFichier($dir, $fileName, $content = '')
    {
        if (is_writable($dir)) {
            if (!$handle = @fopen($dir . $fileName, 'w')) {
                $this->addLogEcriture('Fichier '.$fileName.' non éditable');
                return false;
            }

            if (@fwrite($handle, $content) === false) {
                $this->addLogEcriture('Ecriture dans le fichier '.$fileName.' impossible');
                return false;
            }

            fclose($handle);
            $this->addLogEcriture('Ecriture dans le fichier '.$fileName.' réussie', 'success');
            return true;
        } else {
            $this->addLogEcriture('Ouverture du dossier '.$dir.' impossible.');
            return false;
        }
    }

    /**
     * Déplace un fichier de $depart à $arrivee avec ecrasement si besoin ($ecraser)
     * @param string $depart chemin du fichier intial
     * @param string $arrivee chemin du fihceir final
     * @param boolean $ecraser true si il faut ecraser
     * @return boolean return true si deplacement ok
     */
    public function deplacerFichier($depart, $arrivee, $ecraser = true)
    {
        if (!file_exists($depart)) {
            $this->addLogDeplacement('Fichier demandé ('.$depart.') inexistant.');
            return false;
        }

        if (file_exists($arrivee)) {
            if (!$ecraser) {
                $this->addLogDeplacement(
                    'Déplacement du fichier '.$depart.' impossible, il existe déjà et ne peut être écrasé.'
                );
                return false;
            }
        }

        if (@rename($depart, $arrivee) === true) {
            $msg = 'Fichier ' . $depart . ' déplacé correctement vers' . $arrivee;
            if ($ecraser) {
                $msg .= ', avec écrasement';
            } else {
                $msg .= ', sans écrasement';
            }
            $this->addLogDeplacement($msg, 'success');

            return true;
        } else {
            $msg = 'Fichier ' . $depart . ' non déplacé vers ' . $arrivee . ' ; échec lors du déplacement';
            $this->addLogDeplacement($msg, 'error');
            return false;
        }
    }

    /**
     *
     * @param type $dir
     * @return type
     */
    public function mkmap($dir, $router)
    {
        $top = array();
        $i = 0;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = "$dir/$file";
                
                if (is_dir("$dir/$file")) {
                    $i++;
                    $top[] = array(
                        'name' => $this->removeSpecialsCharacters($file),
                        'open' => false,
                        'icon' => '/bundles/frontofficemain/images/icons/directory_empty2.png',
                        'iconOpen' => '/bundles/frontofficemain/images/icons/directory_open.png',
                        'iconClose' => '/bundles/frontofficemain/images/icons/directory_close.png',
                        'children' =>@ $this->mkmap($filePath, $router)
                    );
                } else {
                    $lastModification = date('d/m/Y H:i:s', filemtime($filePath));
                    $fileSize = filesize($filePath);
                    $icon = '/bundles/frontofficemain/images/icons/file.png';
                    if (ParserManager::endsWith($filePath, '.pdf')) {
                        $icon = '/bundles/backofficemonitoring/images/icon_tree_pdf.png';
                    }

                    $top[] = array(
                        'icon' => $icon,
                        'title' => 'TEST',
                        'url' => $router->generate(
                            'front_office_print_file',
                            array('filePath'=>  base64_encode($filePath))
                        ),
                        'name' => $this->removeSpecialsCharacters($file) . " - " .
                            $lastModification . " (" . $fileSize . " bytes)"
                    );
                }
            }
        }
        
        return $top;
    }
    
    private function removeSpecialsCharacters($name)
    {
        return strtr(
            utf8_decode($name),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        );
    }
}
