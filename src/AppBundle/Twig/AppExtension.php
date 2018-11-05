<?php
// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('remainingDays', array($this, 'nbJours')),
            new \Twig_SimpleFilter('transformFloatToDays', array($this, 'floatToDays')),

        );
    }

    public function floatToDays($estimatedTime){
        $partieEntiere = floor($estimatedTime);
        $partieDecimale = $estimatedTime - $partieEntiere;

        $strRes = '';

        if ($partieEntiere > 0) {
            $strRes = $strRes.$partieEntiere.'j';
            if ($partieDecimale > 0) {
                $strRes .= ' et ';
            }
        }

        if ($partieDecimale > 0) {
            $strRes = $strRes.($partieDecimale*8).'h';
        }

        if ($partieEntiere <= 0 && $partieDecimale <= 0){
            $strRes = '0h';
        }

        return $strRes;
    }

    // calcul des années bissextiles

    /**
     * @param \Datetime $date2
     * @return string
     */
    public function nbJours(\Datetime $date2)
    {
        $today = new \DateTime();

        if ($date2 < $today) {
            $symbol = '+';
            $date1 = $date2;
            $date2 = $today;
        } else {
            $date1 = $today;
            $symbol = '-';
        }

        $date1 = $date1->format('d-n-Y');

        $date2 = $date2->format('d-n-Y');

        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);

        $tot = 0; // total de jours entre les 2 dates

        // dates en jours de l'année ( depuis le 1er jan )
        $date1 = date('z', $timestamp1); // date de depart
        $date2 = date('z', $timestamp2); //date d'arrivée

        $day_stamp = 86400; // (3600 * 24 ) // un journée en timestamp

        // années des deux dates
        $year1 = date('Y', $timestamp1);
        $year2 = date('Y', $timestamp2);

        $num = 0; // nombre de jours feries a compter sur la duree totale
        $counter = 0; // la durée entre deux date par année

        $year = $year1; // l'année en cours ( defaut : $year1 )

        // on calcule le nombre de jours de difference entre les deux dates, en tenant compte des années
        while ($year <= $year2) {
            $date3 = date('d-n-Y', mktime(0, 0, 0, 1, 1, $year));
            $timestamp3 = strtotime($date3); // date de référence pour l'année en cours
            $counter = 0; // compteur de jours pour chaque année

            //on récupère la date de pâques
            $easterDate = easter_date($year);
            $easterDay = date('j', $easterDate);
            $easterMonth = date('n', $easterDate);
            $easterYear = date('Y', $easterDate);

            // le tableau sort les jours fériés de l'année depuis le premier janvier
            $closed = array
            (
                // dates fixes
                date('z', mktime(0, 0, 0, 1, 1, $year)),  // 1er janvier
                date('z', mktime(0, 0, 0, 5, 1, $year)),  // Fête du travail
                date('z', mktime(0, 0, 0, 5, 8, $year)),  // Victoire des alliés
                date('z', mktime(0, 0, 0, 7, 14, $year)),  // Fête nationale
                date('z', mktime(0, 0, 0, 8, 15, $year)),  // Assomption
                date('z', mktime(0, 0, 0, 11, 1, $year)),  // Toussaint
                date('z', mktime(0, 0, 0, 11, 11, $year)),  // Armistice
                date('z', mktime(0, 0, 0, 12, 25, $year)),  // Noel

                // dates basées sur Paques
                date('z', mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)),  // Lundi de Paques
                date('z', mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)), // Ascension
                date('z', mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear))  // Lundi de Pentecote

            );

            // si c'est la première année -> on commence par la date de depart
            // le compteur compte les jours jusqu'au 31dec
            if ($year === $year1 && $year < $year2) {
                $i = $date1;
                $counter += (364 + $this->leap_year($year));
            }

            // si c'est ni la première ni la derniere année -> on commence au premier janvier
            // le compteur compte tous les jours de l'année
            if ($year > $year1 && $year < $year2) {
                $i = date('z', mktime(0, 0, 0, 1, 1, $year));
                $counter += 364 + $this->leap_year($year);
            }

            // si c'est la dernière année -> on commence au premier janvier
            // le compteur va jusqu'a la date d'arrivée
            if ($year === $year2 && $year > $year1) {
                $i = date('z', mktime(0, 0, 0, 1, 1, $year));
                $counter += $date2;
            }
            // si les deux dates sont dans la même année
            if ($year === $year1 && $year === $year2) {
                $i = $date1;
                $counter += $date2;
            }
            // on boucle les jours sur la période donnée
            while ($i <= $counter) {
                ++$tot; // on ajoute 1 pour chaque jour passé en revue

                if (in_array($i, $closed, true)) {
                    $num++; // on ajoute 1 pour chaque jour férié rencontré
                }
                // on compte chaque samedi et chaque dimanche
                if (!in_array($i, $closed, true) && ((date('w', $timestamp3 + $i * $day_stamp) === 6) || (date('w', $timestamp3 + $i * $day_stamp) === 0))) {
                    $num++;
                }

                $i++;
            }

            $year++; // on incremente l'année
        }

        $res = $tot - $num; // nombre de jours entre les 2 dates fournies - nombre de jours non ouvrés
        return  'J' . $symbol . $res;
    }

    // calcul du nombre de jours ouvrés entre 2 dates en tenant compte des jours fériés

    public function leap_year($year)
    {
        return date('L', mktime(0, 0, 0, 1, 1, $year));
    }
}