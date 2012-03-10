<?php
/**
 * *********************************************************
 * 
 * @author cluries
 * @link http://cdbit.com
 * @version 1.0
 * @copyright 2012 http://cdbit.com All rights reserved
 * 
 * *********************************************************
 */

$tpl = getTemplateInstance();

$tpl->locked_some = locked_some();

$needfunctions   = array('curl_init','file_get_contents','file_put_contents','mcrypt_cbc' );
$function_checks = '';
foreach ($needfunctions as $offset => $func) {
	$function_checks .= $offset.' '.$func.':'.(function_exists($func)?'YES':'NO')."<br />";
}
$tpl->function_checks = $function_checks;
$tpl->display('install/default');