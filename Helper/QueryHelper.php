<?php

namespace Rawburner\Helper;

/**
 * Class QueryHelper
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 * @package App\Helper
 */
class QueryHelper
{

    /**
     * @param $tableAlias
     * @author Alexander Keil
     * @return string
     */
    public static function getActiveSelection($tableAlias){
        $today = date('Y-m-d H:i:s');
        return
            sprintf(
                '((%s.active_from IS NULL OR %s.active_from IS NOT NULL AND %s.active_from < %s) AND '.
                '(%s.active_to IS NULL OR %s.active_to IS NOT NULL AND %s.active_to < %s))',
                $tableAlias, $tableAlias, $tableAlias, '"'.$today.'"', $tableAlias, $tableAlias, $tableAlias, '"'.$today.'"'
            );
    }
}
