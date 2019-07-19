<?php

namespace Rawburner\Helper;

use Nette\Utils\Strings;

/**
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 */
class StringHelper extends Strings
{
    public static function makeValidPath(string ...$paths): string
    {
        $outputPath = '';
        foreach ($paths as $path) {
            if (!$outputPath) {
                $outputPath = rtrim($path, '/');

                continue;
            }
            $outputPath .= '/'.trim($path, '/');
        }

        return $outputPath;
    }

    /**
     * @see https://stackoverflow.com/a/3997367/5884988
     */
    public static function explodeByNewLine(string $string): array
    {
        return preg_split('/\r\n|\r|\n/', $string);
    }

    public static function explodeByComma(string $string): array
    {
        $strings = preg_split('/;|,/', $string);
        array_walk($strings, function (&$var): void {
            $var = trim($var);
        });

        return $strings;
    }

    public static function convertSummernoteContent(string $content): string
    {
        $content = urldecode($content);
        /** Editor hinterlässt viele Leerzeichen vor dem HTML */
        $content = trim($content);
        /** Internet-Explorer macht strong statt b */
        $content = str_replace('</strong>', '</b>', $content);
        $content = preg_replace('`(<strong)([^\w])`i', '<b$2', $content);

        /** Absätze zu Zeilenumbrüchen machen */
        $content = str_replace(['<p>', '</p>'], ['','<br>'], $content);

        /** entfernen von leeren tags, können auch Attribute enthalten */
        $content = preg_replace("/<p[^>]*(?:\/>|>(?:\s|&nbsp;|﻿)*<\/p>)/m", '', $content);
        $content = preg_replace("/<span[^>]*(?:\/>|>(?:\s|&nbsp;)*<\/span>)/m", '', $content);
        return $content;
    }

    public static function getProfileNameFromXingUrl(string $xingUrl): string
    {
        if(!$xingUrl){
            return '';
        }
        preg_match('#www.xing.com\/profile\/(.*)\/?#', $xingUrl, $matches);
        if (!count($matches)) {
            return '';
        }
        $matchedProfile = rtrim($matches[1], '/');
        if (strpos($matchedProfile, '/')) {
            $matchedProfile = explode('/', $matchedProfile)[0];
        }

        return $matchedProfile;
    }

    public static function htmlToText(string $html): string
    {
        $output = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
        $output = html_entity_decode($output);

        return strip_tags($output);
    }

    public static function subStrWordWrap(string $text, int $len): string
    {
        if (\strlen($text) > $len) {
            $text = str_replace(["\n\r", "\r\n", "\n", "\r"], ' ', $text);
            $matches = [];
            preg_match("/^(.{1,${len}})[\\s]/iu", $text, $matches);
            if (!isset($matches[0])) {
                return $text;
            }

            return trim($matches[0].'...');
        }

        return $text;
    }

    /**
     * Parse address and split street and housenumber
     * Taken from DHL Intraship.
     *
     * @see https://github.com/quafzi/magento-dhl-intraship/blob/official/app/code/community/Dhl/Intraship/Helper/Data.php
     */
    public static function splitStreet(string $street): array
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | care_of                 | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */
        if (preg_match("/^([^0-9]+)([0-9]+[ ])*[ \t]*([0-9]*[-\\w^.]*)?[, \t]*([^0-9]+.*)?\$/", $street, $matches)) {
            //check if street has additional value and add it to streetname
            if (preg_match('/^([0-9]+)?$/', trim($matches[2]))) {
                $matches[1] = $matches[1].$matches[2];
            }

            return [
                'street_name' => trim($matches[1]),
                'street_number' => isset($matches[3]) ? $matches[3] : '',
                'care_of' => isset($matches[4]) ? trim($matches[4]) : '',
            ];
        }

        return [
            'street_name' => $street,
            'street_number' => '',
            'care_of' => '',
        ];
    }

    public static function filterHTMLText(string $text): string
    {
        return strip_tags($text, '<p><h2><h3><h4><h5><h6><br><br/><ul><ol><li><i><em><a><strong><b><u>');
    }

    public static function makeValidFilename(string $filename): string
    {
        $translitTable = TranslitTable::getTranlitTable();
        $filename = str_replace(array_keys($translitTable), array_values($translitTable), $filename);

        return preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
    }

    public static function filterSword(string $sword): string
    {
        $sword = trim(strip_tags(htmlspecialchars_decode(stripslashes($sword))));

        return str_replace('/', ' ', $sword);
    }

    public static function human_filesize(string $bytes, ?int $decimals = 2): string
    {
        $sz = ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = (int) floor((\strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).$sz[$factor];
    }

    public static function csv_to_array(string $string = '', string $row_delimiter = PHP_EOL, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): array
    {
        $rows = array_filter(explode($row_delimiter, $string));
        $header = null;
        $data = [];

        foreach ($rows as $row) {
            $row = str_getcsv($row, $delimiter, $enclosure, $escape);
            if (!$header) {
                $header = $row;
            } elseif(count($row)===count($header)) {
                $data[] = array_combine($header, $row);
            }
        }

        return $data;
    }

    public static function decompress(string $compressed): string
    {
        $compressed = explode(',', $compressed);
        $dictSize = 256;
        $dictionary = [];
        for ($i = 1; $i < 256; ++$i) {
            $dictionary[$i] = \chr($i);
        }
        $w = \chr($compressed[0]);
        $result = $w;
        for ($i = 1; $i < \count($compressed); ++$i) {
            $k = $compressed[$i];
            if (isset($dictionary[$k])) {
                $entry = $dictionary[$k];
            } elseif ($k === $dictSize) {
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

    public static function charAt(string $string, $index): int
    {
        if ($index < mb_strlen($string)) {
            return mb_substr($string, $index, 1);
        }

        return -1;
    }
}
