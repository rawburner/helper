<?php

namespace Rawburner\Helper;

use Assert\Assertion;

/**
 * Class DateHelper
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 * @package Rawburner\Helper
 */
class DateHelper
{
    /**
     * Ermittelt die Werktage zwischen zwei Datumswerten
     * @param $startDate
     * @param $endDate
     * @author Alexander Keil
     * @return array|\DateTime[]
     * @throws \Exception
     */
    public static function getWeekdaysBetweenDates($startDate, $endDate){
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

    /**
     * @param $number
     * @return mixed
     */
    public static function getMonthGermanFormat($number){
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
     * @param $date
     * @param string $format
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return bool
     * @see https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
     */
    public static function isDateValid($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * @param $date
     * @param string $format
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return bool
     */
    public static function isDateInFuture($date){
        $current_date = new \DateTime();
        $current_date->setTime(0,0,0);
        $check_date = self::convertDateStringToObject($date);
        $check_date->setTime(0,0,0);

        return $check_date > $current_date;
    }

    /**
     * Datum vergleichen
     * @param $datetime1
     * @param $datetime2
     * @param string $format
     * @return bool
     */
    public static function isSameDateTime($datetime1, $datetime2, $format = 'd.m.Y'){
        $datetime1 = self::convertDateStringToObject($datetime1);
        $datetime2 = self::convertDateStringToObject($datetime2);
        if(!$datetime1 || $datetime2){
            return false;
        }
        return $datetime1->format($format) == $datetime2->format($format);
    }

    /**
     * Konvertiert einen String in ein DateTime-Objekt
     * @param $dateString
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return bool|\DateTime|null
     */
    public static function convertDateStringToObject($dateString){
        if($dateString instanceof \DateTime){
            return $dateString;
        }
        try{
            Assertion::notEmpty($dateString);
            $timestamp = strtotime($dateString);
            Assertion::notEq($timestamp, false, 'Datumsangabe konnte nicht validiert werden: '.$dateString);
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($timestamp);
            return $dateTime;
        }catch (\Throwable $exception){
            return null;
        }
    }
}
