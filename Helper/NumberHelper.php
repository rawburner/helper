<?php

namespace Rawburner\Helper;

/**
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 */
class NumberHelper
{

    public static function formatPrice(float $price, ?int $minDecimals=2): string
    {
        //https://stackoverflow.com/a/14531760/5884988
        $price = $price + 0;
        $split = explode('.', $price);
        return number_format($price, (isset($split[1]) && strlen($split[1]) > $minDecimals) ? strlen($split[1]) : $minDecimals, ',', '.');
    }

    /**
     * @see http://php.net/manual/de/function.floatval.php#114486
     * @param mixed $num
     */
    public static function tofloat($num):float
    {
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
