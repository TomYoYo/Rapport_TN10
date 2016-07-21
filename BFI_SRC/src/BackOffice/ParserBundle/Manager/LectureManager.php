<?php

namespace BackOffice\ParserBundle\Manager;

use BackOffice\ParserBundle\Manager\ParserManager;

/**
 * Description of LectureManager
 *
 * @author j.david
 */
class LectureManager extends ParserManager
{
    public $numLine = 0;
    public $currentLine = '';

    public function setCurrentLine($content, $i)
    {
        $this->currentLine = $content;
        $this->numLine = $i;
    }

    public function getDate($deb = 0)
    {
        $date = mb_substr($this->currentLine, $deb, 10, 'utf-8');
        $dateTab = date_parse_from_format("d/m/Y", $date);

        if ($dateTab['error_count'] > 0) {
            $this->addAlert('Format date non correct ('.$date.'). Remplacé par 00/00/0000 ', 'Lecture de fichier');
            $date = "00/00/0000";
        }

        return $date;
    }
}
