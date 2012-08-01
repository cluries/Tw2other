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

date_default_timezone_set ( trim ( defined ( 'DATE_ZONE' ) ? DATE_ZONE : 'Etc/GMT-8' ) );

include BASE_PATH . '/modules/init/func.inc.php';
set_exception_handler ( 'exceptionHandler' );
set_error_handler ( 'errorHandler', E_ALL ^ E_NOTICE );

import ('/modules/init/Loader.php');

//Loader::getInstance ()

?>