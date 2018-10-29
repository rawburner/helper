<?php

namespace Rawburner\Helper;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Alexander Keil (alexanderkeil80@gmail.com)
 * @package Rawburner\Helper
 */
class ExpressionLanguageHelper
{

    protected $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->addCustomFunctions();
    }

    /**
     * Hinzufügen von individuellen Funktionen für die Expression Syntax
     * @see https://symfony.com/doc/current/components/expression_language/extending.html
     * @author Alexander Keil (alexanderkeil80@gmail.com)
     */
    protected function addCustomFunctions(){
        $this->addSubstrFunction();
    }

    protected function addSubstrFunction(){
        $this->expressionLanguage->register('substr', function ($str) {
            return sprintf('(is_string(%1$s) ? substr(%1$s) : %1$s)', $str);
        }, function ($arguments, $str, $start, $length) {
            if (!is_string($str)) {
                return $str;
            }
            return substr($str, $start, $length);
        });
    }

    /**
     * @return mixed
     */
    public function evaluate(string $expression, array $params){
        return $this->expressionLanguage->evaluate($expression, $params);
    }
}
