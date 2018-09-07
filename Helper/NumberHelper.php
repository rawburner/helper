<?php

namespace Rawburner\Helper;

/**
 * Class NumberHelper
 * @author Alexander Keil (alexanderkeil80@gmail.com)
 * @package Rawburner\Helper
 */
class NumberHelper
{

    /**
     * Entnommen aus Shopware
     * @param $price
     * @return float|mixed|string
     */
    public static function formatPrice($price)
    {
        $price = str_replace(',', '.', $price);
        $price = self::round($price);
        $price = str_replace('.', ',', $price);
        $commaPos = strpos($price, ',');
        if ($commaPos) {
            $part = substr($price, $commaPos + 1, strlen($price) - $commaPos);
            switch (strlen($part)) {
                case 1:
                    $price .= '0';
                    break;
                case 2:
                    break;
            }
        } else {
            if (!$price) {
                $price = '0';
            } else {
                $price .= ',00';
            }
        }

        return $price;
    }

    /**
     * Erstellt aus beliebigen Zahlen ein richtiges float
     * @see http://php.net/manual/de/function.floatval.php#114486
     * @param $num
     * @return float
     */
    public static function tofloat($num){
        if(!$num){
            return 0.0;
        }
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }


    /**
     * Entnommen aus Shopware
     * @param null $moneyfloat
     * @return float
     */
    public static function round($moneyfloat = null)
    {
        if (is_numeric($moneyfloat)) {
            $moneyfloat = sprintf('%F', $moneyfloat);
        }
        $money_str = explode('.', $moneyfloat);
        if (empty($money_str[1])) {
            $money_str[1] = 0;
        }
        $money_str[1] = substr($money_str[1], 0, 3);

        $money_str = $money_str[0] . '.' . $money_str[1];

        return round($money_str, 2);
    }

}
