<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cat<br>
 * Date:     Feb 24, 2003
 * Purpose:  catenate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 * @link http://smarty.php.net/manual/en/language.modifier.cat.php cat
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_redir($string ,$type)
{
    $root = '';
    $urlMode = FLEA::getAppInf('urlMode');
    if($urlMode == 'URL_PATHINFO' && defined('__ROOT__') && __ROOT__){
        $root = __ROOT__. '/';
    }

    return $root .ltrim($string,'/');
}

/* vim: set expandtab: */

?>
