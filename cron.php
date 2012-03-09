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


$url = '';


$curlHandler = curl_init($url);
curl_setopt ( $curlHandler, CURLOPT_RETURNTRANSFER, true );
curl_exec  ( $curlHandler );
curl_close ( $curlHandler );

?>