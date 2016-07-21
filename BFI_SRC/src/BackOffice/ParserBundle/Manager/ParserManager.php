<?php

namespace BackOffice\ParserBundle\Manager;

/**
 * Description of ParserManager
 *
 * @author j.david
 */
class ParserManager
{
    public $error = array();

    public function __construct($lm)
    {
        $this->logManager = $lm;
    }

    public function addAlert($e, $action)
    {
        $this->logManager->addAlert($e, "BackOffice > Parser", $action);
        $this->addError($e);
    }

    public function addError($e)
    {
        $this->error[] = $e;
    }

    public function getErrors()
    {
        return $this->error;
    }

    /**
     *
     * @param type $string : 28/12/2014
     * @param bool $debut
     * @return \DateTime
     */
    public static function transformDate($string, $debut = true)
    {
        $day = substr($string, 0, 2);
        $month = substr($string, 3, 2);
        $year = substr($string, 6);

        $d = new \DateTime();
        $d->setDate($year, $month, $day);
        if ($debut) {
            $d->setTime(0, 0, 0);
        } else {
            $d->setTime(23, 59, 0);
        }

        return $d;
    }
    
    /**
    * Ajout car format de date dans SAB = CYYMMDD
    *
    * @author jc.forest
    */
    public static function transformDateToCYYMMDD($string, $debut = true)
    {
        $day = substr($string, 0, 2);
        $month = substr($string, 3, 2);
        $yearbig = substr($string, 6);
        $yearmini = substr($string, 8);
        $str = (date($yearbig) >= 2000? 1 : 0) . date($yearmini.$month.$day);
        
        return $str;
    }
    
     /**
     *
     * @param type $string : 1150831
     * @param bool $debut
     * @return \DateTime
     */
    public static function transformDateSABToYdm($string, $debut = true)
    {
        $day = substr($string, -2);
        $month = substr($string, -4, 2);
        $year = '20'.substr($string, -6, 2);

        $d = new \DateTime();
        $d->setDate($year, $month, $day);
        if ($debut) {
            $d->setTime(0, 0, 0);
        } else {
            $d->setTime(23, 59, 0);
        }

        return $d;
    }
    
    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public static function getDateTimeFromUniJobReport($str)
    {
        $tmp = explode(' ', $str);
        if (count($tmp) < 2) {
            return false;
        }

        $res = new \DateTime();
        $date = explode('/', $tmp[0]);

        $res->setDate($date[2], $date[1], $date[0]);

        $time = explode(':', $tmp[1]);

        $res->setTime($time[0], $time[1], $time[2]);

        return $res;
    }
}
