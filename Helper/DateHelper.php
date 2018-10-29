<?php

namespace Rawburner\Helper;

use Assert\Assertion;
use Nette\Utils\DateTime;

/**
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 * @package Rawburner\Helper
 */
class DateHelper extends DateTime
{
    /**
     * Ermittelt die Werktage zwischen zwei Datumswerten
     * @param string|\DateTime $startDate
     * @param string|\DateTime $endDate
     */
    public static function getWeekdaysBetweenDates($startDate, $endDate):array{
        $weekDays = [];
        $period = new \DatePeriod(
            self::convertDateStringToObject($startDate),
            new \DateInterval('P1D'),
            self::convertDateStringToObject($endDate)->setTime(23,59,59)
        );

        /** @var \DateTime $date */
        foreach ($period as $date){
            if(in_array($date->format('N'), [6,7])){
                continue;
            }
            $weekDays[]=$date;
        }
        return $weekDays;
    }

    public static function getMonthGermanFormat(string $number):string{
        $arMonths = [
            1 => 'Januar',
            2 => 'Februar',
            3 => 'März',
            4 => 'April',
            5 => 'Mai',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'August',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Dezember'

        ];

        return $arMonths[$number];
    }

    /**
     * @param string|\DateTime $date
     * @see https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
     */
    public static function isDateValid($date, string $format = 'Y-m-d'):bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * @param string|\DateTime $date
     */
    public static function isDateInFuture($date):bool{
        $current_date = new \DateTime();
        $current_date->setTime(0,0,0);
        $check_date = self::convertDateStringToObject($date);
        $check_date->setTime(0,0,0);

        return $check_date > $current_date;
    }

    /**
     * @param string|\DateTime $datetime1
     * @param string|\DateTime $datetime2
     */
    public static function isSameDateTime($datetime1, $datetime2, string $format = 'd.m.Y'):bool {
        $datetime1 = self::convertDateStringToObject($datetime1);
        $datetime2 = self::convertDateStringToObject($datetime2);
        if(!$datetime1 || $datetime2){
            return false;
        }
        return $datetime1->format($format) == $datetime2->format($format);
    }

    /**
     * Konvertiert einen String in ein DateTime-Objekt
     * @param string|\DateTime $dateString
     */
    public static function convertDateStringToObject($dateString):\DateTime{
        if($dateString instanceof \DateTime){
            return $dateString;
        }
        Assertion::notEmpty($dateString);
        $timestamp = strtotime($dateString);
        Assertion::notEq($timestamp, false, 'Datumsangabe konnte nicht validiert werden: '.$dateString);
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime;
    }
}
