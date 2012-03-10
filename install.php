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

define ( 'START_TIME', microtime ( true ) );
define ( 'BASE_PATH', dirname ( __FILE__ ) );
define ( 'DEBUG', true );

session_start ();

include BASE_PATH . '/config/config.inc.php';
include BASE_PATH . '/modules/init/bootstarp.php';

//display('Error Processing Request',0);
//trigger_error("error_msg");
//throw new Exception("Error Processing Request", 1);


$action = getVar ( 'action', 'default' );
$actionFile = "/modules/install/{$action}Action.php";


$actionVerify = '/^[a-zA-Z0-9]+$/';
preg_match ( $actionVerify, $action ) && ttimport ( $actionFile ) || display ( _er ( 'E_404' ), 0 );


?>