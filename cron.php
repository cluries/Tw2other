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

define ( 'BASE_PATH', dirname ( __FILE__ ) );
set_time_limit(240);

include BASE_PATH . '/config/config.inc.php';
$url = BASE_URL.'/run.php';

 


$curlHandler = curl_init($url);
curl_setopt ( $curlHandler, CURLOPT_RETURNTRANSFER, true );
curl_exec  ( $curlHandler );
curl_close ( $curlHandler );

?>