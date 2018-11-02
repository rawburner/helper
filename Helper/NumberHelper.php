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
     */
    public static function formatPrice(string $price):string
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
     * @see http://php.net/manual/de/function.floatval.php#114486
     * @param mixed $num
     */
    public static function tofloat($num):float{
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
     * @param mixed $moneyfloat
     */
    public static function round($moneyfloat = null):float
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
