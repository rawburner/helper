<?php

namespace Rawburner\Helper;

/**
 * Eine Sammlung von oft gebrauchten Text-Funktionen
 * Class TextOperations
 * @package CommonBundle\Helper
 */
class StringHelper
{

    /**
     * @param $xingUrl
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return string
     */
    public static function getProfileNameFromXingUrl($xingUrl){
        preg_match('#www.xing.com\/profile\/(.*)\/?#', $xingUrl, $matches);
        if(!is_array($matches) || !$matches[0] || !$matches[1]){
            return '';
        }
        $matchedProfile = rtrim($matches[1], '/');
        if(strpos($matchedProfile, '/')){
            $matchedProfile = explode('/', $matchedProfile)[0];
        }
        return $matchedProfile;
    }

    /**
     * @param $html
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return string
     */
    public static function htmlToText($html){
        $output = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
        $output = html_entity_decode($output);
        return strip_tags($output);
    }


    /**
     * Cut text without split a word
     * @author Alexander Keil
     * @param $text
     * @param $len
     * @return mixed
     */
    public static function subStrWordWrap($text, $len){
        if(strlen($text) > $len) {
            $text = str_replace(["\n\r", "\r\n", "\n", "\r"], " ", $text);
            $matches = [];
            preg_match("/^(.{1,$len})[\\s]/iu", $text, $matches);
            return trim($matches[0].'...');
        }else{
            return $text;
        }
    }


    /**
     * Parse address and split street and housenumber
     * Taken from DHL Intraship
     * @see https://github.com/quafzi/magento-dhl-intraship/blob/official/app/code/community/Dhl/Intraship/Helper/Data.php
     * @param $street
     * @return array
     */
    public static function splitStreet($street)
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | care_of                 | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */

        preg_match("/^([^0-9]+)[ \t]*([-\w^.]+)[, \t]*([^0-9]+.*)?\$/", $street, $matches);
        unset($matches[0]);
        $parts = array_reverse($matches);

        $current = 'care_of';
        $splittedStreet = [
            'street_name'   => '',
            'street_number' => '',
            'care_of'       => ''
        ];
        foreach ($parts as $value) {
            if ('care_of' == $current) {
                if (is_numeric(substr($value, 0, 1))) {
                    $current = 'street_number';
                }
            }
            if ('street_number' == $current && false === is_numeric(substr($value, 0, 1))) {
                $current = 'street_name';
            }
            $splittedStreet[$current] = trim($value . ' ' . $splittedStreet[$current]);
        }
        return $splittedStreet;
    }


    /**
     * Filter not allowed HTML Tags
     * @author Alexander Keil
     * @param $text
     * @return string
     */
    public static function filterHTMLText($text){
        $filteredText = strip_tags($text, '<p><h2><h3><h4><h5><h6><br><br/><ul><ol><li><i><em><a><strong><b><u>');
        return $filteredText;
    }

    /**
     * Clear the filename from special chars
     * @author Alexander Keil
     * @param $filename
     * @return mixed
     */
    public static function makeValidFilename($filename){
        $translitTable = TranslitTable::getTranlitTable();
        $filename = str_replace(array_keys($translitTable), array_values($translitTable), $filename);
        return preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
    }

    /**
     * Filtert einen Suchbegriff, um fehlerhafte URLs zu vermeiden
     * @param $sword
     * @return string
     */
    public static function filterSword($sword)
    {
        $sword = trim(strip_tags(htmlspecialchars_decode(stripslashes($sword))));

        return str_replace('/', ' ', $sword);
    }


    /**
     * @param $bytes
     * @param int $decimals
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return string
     */
    public static function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sz[$factor];
    }

    /**
     * @param string $string
     * @param string $row_delimiter
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     * @return array
     */
    public static function csv_to_array($string='', $row_delimiter=PHP_EOL, $delimiter = "," , $enclosure = '"' , $escape = "\\" )
    {
        $rows = array_filter(explode($row_delimiter, $string));
        $header = NULL;
        $data = [];

        foreach($rows as $row){
            $row = str_getcsv ($row, $delimiter, $enclosure , $escape);
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        return $data;
    }

    /**
     * @see https://webdevwonders.com/lzw-compression-and-decompression-with-javascript-and-php/
     * @param $compressed
     * @return null|string
     */
    public static function decompress($compressed) {
        $compressed = explode(",", $compressed);
        $dictSize = 256;
        $dictionary = [];
        for ($i = 1; $i < 256; $i++) {
            $dictionary[$i] = chr($i);
        }
        $w = chr($compressed[0]);
        $result = $w;
        for ($i = 1; $i < count($compressed); $i++) {
            $k = $compressed[$i];
            if (isset($dictionary[$k])) {
                $entry = $dictionary[$k];
            } else if ($k == $dictSize) {
                $entry = $w.self::charAt($w, 0);
            } else {
                return null;
            }
            $result .= $entry;
            $dictionary[$dictSize++] = $w.self::charAt($entry, 0);
            $w = $entry;
        }
        return $result;
    }

    /**
     * @param $string
     * @param $index
     * @return int|string
     */
    public static function charAt($string, $index){
        if($index < mb_strlen($string)){
            return mb_substr($string, $index, 1);
        } else{
            return -1;
        }
    }

}
