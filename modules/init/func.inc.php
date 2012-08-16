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

! defined ( 'BASE_PATH' ) && exit ();

function import($file)
{
	if (file_exists ( BASE_PATH . $file )) {
		include_once BASE_PATH . $file;
		return 1;
	}
	
	throw new TOException ( "File: {$file} not exists." );
}

function ttimport($file)
{
	if (file_exists ( BASE_PATH . $file )) {
		include BASE_PATH . $file;
		return 1;
	}
	 
	return 0;
}

function L($info)
{
	$logFile = tmpDir ( 'logs/Log_' . date ( "Y_m_d" ) . '.log' );
	$info = "[" . date ( "Y-m-d H:i:s" ) . "] {$info} \n";
	file_put_contents ( $logFile, $info, FILE_APPEND );
}

function islocked($type)
{
	return file_exists ( tmpDir ( "locks/{$type}.lock" ) );
}

function lockit($type)
{
	file_put_contents ( tmpDir ( "locks/{$type}.lock" ), time () );
}

function tmpDir($filename = null)
{
	if (! defined ( 'TMPDIR' )) {
		throw new TOException ( 'TMPDIR is empty.' );
	}
	
	if (substr ( TMPDIR, 0, 1 ) == '/') {
		return TMPDIR . '/' . $filename;
	}
	
	return BASE_PATH . '/' . TMPDIR . '/' . $filename;
}

function runTime()
{
	if (! defined ( 'START_TIME' )) {
		return "unkown time";
	}
	
	return sprintf ( "%.4f seconds", microtime ( true ) - START_TIME );
}

function getTemplateInstance($templateDir = '/template')
{
	$tpl = new Template ();
	$tpl->setCaching ( false );
	$templateDir = BASE_PATH . $templateDir;
	$tpl->setTemplateDir ( $templateDir );
	$tpl->setSkin ( 'default' );
	$tpl->debug = defined ( 'APPLICATION_ENV' ) && (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing');
	
	return $tpl;
}

function display($message, $face = 1, $exit = true)
{
	$tpl = getTemplateInstance ();
	
	$tpl->message = $message;
	$tpl->face = $face;
	
	$tpl->display ( 'message' );
	
	$exit && exit ();
}

function getParam($name, $default = null)
{
	if (isset ( $_POST [$name] )) {
		return $_POST [$name];
	}
	
	if (isset ( $_GET [$name] )) {
		return $_GET [$name];
	}
	
	return $default;
}

function getVar($name, $default = null, $callbacks = 'trim')
{
	$val = getParam ( $name, $default );
	if (empty ( $callbacks ) || $val === $default) {
		return $val;
	}
	
	if (is_array ( $callbacks )) {
		foreach ( $callbacks as $callback ) {
			$callback = ( string ) $callback;
			if (is_array ( $val )) {
				$val = array_map ( $callback, $val );
				continue;
			}
			$val = call_user_func ( $callback, $val );
		}
		
		return $val;
	}
	
	$callbacks = ( string ) $callbacks;
	if (is_array ( $val )) {
		$val = array_map ( $callbacks, $val );
		return $val;
	}
	
	return call_user_func ( $callbacks, $val );
}

function errorHandler($errno, $errstr, $errfile, $errline)
{
	try {
		L ( $errstr );
		$tpl = getTemplateInstance ();
		$tpl->title = _er ( 'E_ERROR_OCCUR' );
		
		if ($tpl->debug) {
			$tpl->error = "Fatal error:[{$errno}] {$errstr} in {$errfile} on line {$errline}";
		} else {
			$tpl->error = "Fatal error:[{$errno}] {$errstr}";
			$exception ['errno'] = $errno;
			$exception ['errstr'] = $errstr;
			$exception ['errfile'] = $errfile;
			$exception ['errline'] = $errline;
			
			$tpl->exception = $exception;
		}
		
		$tpl->display ( 'exception' );
	
	} catch ( Exception $e ) {
		echo $e->getMessage ();
	}
	
	exit ();
}

function exceptionHandler(Exception $ex)
{
	try {
		L ( $ex->getMessage () . "\n" . $ex->getTraceAsString () . "\n\n" );
		
		$tpl = getTemplateInstance ();
		
		$tpl->title = _er ( 'E_EXCEPTION_NOT_CATCH' );
		$tpl->debug = defined ( 'APPLICATION_ENV' ) && (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing');
		$tpl->exception = $ex;
		$tpl->error = $ex->getMessage ();
		$tpl->display ( 'exception' );
	} catch ( Exception $e ) {
		echo $e->getMessage ();
	}
	
	exit ();
}

function _er()
{
	static $__static_errors__ = null;
	if (empty ( $__static_errors__ )) {
		$__static_errors__ = include BASE_PATH . '/modules/error/error.php';
	}
	
	$args = func_get_args ();
	$argsNum = func_num_args ();
	
	if ($argsNum < 2) {
		return $__static_errors__ [$args [0]];
	}
	
	$args [0] = $__static_errors__ [$args [0]];
	return call_user_func_array ( 'sprintf', $args );
}

function debugIncludes()
{
	$includes = get_included_files ();
	echo '<pre style="border:#CCC 1px solid;padding:5px;background:#FFF">';
	$offset = 1;
	foreach ( $includes as $file ) {
		echo $offset ++ . "\t" . $file . "\n";
	}
	echo "</pre>";
}

function S($source, $subdir = 'template/default')
{
	if (! defined ( 'BASE_URL' )) {
		return $source;
	}
	
	return BASE_URL . "/{$subdir}/{$source}";
}

function callbackUrl($type)
{
	return BASE_URL . "/install.php?action={$type}Callback";
}

function locked_some()
{
	$srcs = array ('twitter', 'sina', 'tencent', 'renren', 'fanfou', 'douban' );
	foreach ( $srcs as $key => $value ) {
		if (! islocked ( $value )) {
			unset ( $srcs [$key] );
		}
	}
	
	return implode ( ',', $srcs );
}

function httpRequest($url, $params = null, $decode = 0) 
{
		$curlHandle = curl_init ( $url );
		if (! empty ( $params )) {
			curl_setopt ( $curlHandle, CURLOPT_POST, true );
			if (is_array ( $params )) {
				$params = http_build_query ( $params );
			}
			
			curl_setopt ( $curlHandle, CURLOPT_POSTFIELDS, $params );
		}

		curl_setopt ( $curlHandle, CURLOPT_ENCODING, 'UTF-8' );
		curl_setopt ( $curlHandle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curlHandle, CURLOPT_TIMEOUT, 60 );

		$response = curl_exec ( $curlHandle );
		curl_close ( $curlHandle );
		$response = trim ( $response );
		

		switch ($decode) {
			case 0 :
				return $response;
				break;
			case 1 :
				return json_decode ( $response );
				break;
			case 2 :
				return json_decode ( $response, true );
				break;
			default :
				return $response;
				break;
		}
}

function httpBuildQuery($params,$encode = false)
{
	if ($encode) {
		return http_build_query($params);
	}

	if(!is_array($params)) {
		return  $params;
	}

	$query = '';
	foreach ($params as $key => $value) {
		$query .= "{$key}={$value}&";
	}

	if (!empty($query)) {
		$query = substr($query, 0, -1);
	}

	return $query;
}

class TOException extends Exception
{

}