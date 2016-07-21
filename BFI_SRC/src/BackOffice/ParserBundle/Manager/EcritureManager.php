<?php

namespace BackOffice\ParserBundle\Manager;

use BackOffice\ParserBundle\Manager\ParserManager;

/**
 * Description of ecritureManager
 *
 * @author j.david
 */
class EcritureManager extends ParserManager
{
    public $content = array();

    public function getContent()
    {
        return $this->content;
    }

    public function ecrireLigneSortie($content, $numLigne, $deb = 0, $longueur = 0)
    {
        // init la ligne si besoin
        if (!isset($this->content[$numLigne])) {
            $this->content[$numLigne] = '';
        }

        // concat blanc si besoin
        $nbEspace = $deb - mb_strlen($this->content[$numLigne]) - 1;
        for ($i = 0; $i < $nbEspace; $i++) {
            $this->content[$numLigne] .= ' ';
        }

        // coupe si besoin et si longueur specifiee
        if ($longueur > 0 && mb_strlen($content) > $longueur) {
            $this->addAlert('Ligne '.$numLigne.' trop longue vu la demande. Contenu tronqué.', 'Ecriture de fichier');
            $content = substr($content, 0, $longueur);
        }

        $this->content[$numLigne] .= $content;
    }

    public function ecrireMontant($content, $numLigne, $deb = 0, $longueur = 18)
    {
        //$content = number_format((float)$content, 2, ',', ' ');
        $content = $this->addEspace(trim($content), $longueur, true);
        $this->ecrireLigneSortie($content, $numLigne, $deb, $longueur);
    }

    public function ecrireDateFromSAB($content, $numLigne, $deb = 0, $longueur = 10)
    {
        $date = self::transformerDateSab2Human($content);
        $this->ecrireLigneSortie($date, $numLigne, $deb, $longueur);
    }

    public function addCaractere($content, $taille, $caractere = '0', $coteGauche = true)
    {
        // on met au propre la chaine avant de la completer
        $content = trim($content);
        // on complete la chaine
        while (mb_strlen($content, 'utf-8') < $taille) {
            if ($coteGauche) {
                $content = $caractere . $content;
            } else {
                $content = $content . $caractere;
            }
        }
        return $content;
    }

    public function addZero($content, $taille)
    {
        return $this->addCaractere($content, $taille);
    }

    public function addEspace($content, $taille, $coteGauche = false)
    {
        return $this->addCaractere($content, $taille, ' ', $coteGauche);
    }

    public function getContentHTML()
    {
        $c = implode("\n", $this->content);
        return str_replace(' ', '<i style="color:#AAA;">#</i>', $c);
    }

    public function ecrireNbPage($nb, $nbChar)
    {
        $nbPage = $this->addZero($nb, 3);
        $this->content = str_replace($nbChar, $nbPage, $this->content);
    }

    public function centrerEspace($s, $l)
    {
        if (strlen($s) > $l) {
            return mb_substr($s, 0, $l);
        }

        $nbEspace = round(($l - strlen($s)) / 2);
        for ($i = 0; $i < $nbEspace; $i++) {
            $s = ' ' . $s . ' ';
        }
        return $s;
    }

    public static function asLetters($number, $brute = true)
    {
        $convert = explode('.', $number);
        $convertVirgule = explode(',', $number);
        $num[17] = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit',
            'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize');

        $num[100] = array(20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
            60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix');

        if (isset($convert[1]) && $convert[1] != '') {
            return self::asLetters($convert[0]) . ' euros et ' . self::asLetters($convert[1]) . ' centimes';
        }

        if (isset($convertVirgule[1]) && $convertVirgule[1] != '') {
            return self::asLetters($convertVirgule[0]) . ' euros et ' . self::asLetters($convertVirgule[1]) .
                ' centimes';
        }

        if ($number < 0) {
            return 'moins ' . self::asLetters(-$number);
        }
        if ($number < 17) {
            return $num[17][$number];
        } elseif ($number < 20) {
            return 'dix-' . self::asLetters($number - 10);
        } elseif ($number < 100) {
            if ($number % 10 == 0) {
                return $num[100][$number];
            } elseif (substr($number, -1) == 1) {
                if (((int) ($number / 10) * 10) < 70) {
                    return self::asLetters((int) ($number / 10) * 10) . '-et-un';
                } elseif ($number == 71) {
                    return 'soixante-et-onze';
                } elseif ($number == 81) {
                    return 'quatre-vingt-un';
                } elseif ($number == 91) {
                    return 'quatre-vingt-onze';
                }
            } elseif ($number < 70) {
                return self::asLetters($number - $number % 10) . '-' . self::asLetters($number % 10);
            } elseif ($number < 80) {
                return self::asLetters(60) . '-' . self::asLetters($number % 20);
            } else {
                return self::asLetters(80) . '-' . self::asLetters($number % 20);
            }
        } elseif ($number == 100) {
            return 'cent';
        } elseif ($number < 200) {
            return self::asLetters(100) . ' ' . self::asLetters($number % 100);
        } elseif ($number < 1000) {
            return self::asLetters((int) ($number / 100)) . ' ' .
                self::asLetters(100) . ($number % 100 > 0 ? ' ' .
                    self::asLetters($number % 100) : '');
        } elseif ($number == 1000) {
            return 'mille';
        } elseif ($number < 2000) {
            return self::asLetters(1000) . ' ' . self::asLetters($number % 1000) . ' ';
        } elseif ($number < 1000000) {
            return self::asLetters((int) ($number / 1000)) . ' ' .
                self::asLetters(1000) . ($number % 1000 > 0 ? ' ' .
                    self::asLetters($number % 1000) : '');
        } elseif ($number == 1000000) {
            if ($brute) {
                return 'un million';
            } else {
                return 'millions';
            }
        } elseif ($number < 2000000) {
            return self::asLetters(1000000) . ' ' . self::asLetters($number % 1000000);
        } elseif ($number < 1000000000) {
            return self::asLetters((int) ($number / 1000000), false) . ' ' .
                self::asLetters(1000000, false) . ($number % 1000000 > 0 ? ' ' .
                    self::asLetters($number % 1000000) : '');
        }
    }

    public static function transformerDateSab2Human($dateSab)
    {
        // SAB : 1AAMMJJ
        $annee = '20' . substr($dateSab, 1, 2);
        $mois = substr($dateSab, 3, 2);
        $jour = substr($dateSab, 5, 2);

        // nous JJ/MM/YYYY
        return $jour . '/' . $mois . '/' . $annee;
    }
}
