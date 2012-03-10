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

 

if (islocked ( 'renren' )) {
	display ( _er ( 'E_LOCKED', 'renren', 'renren' ), 0 );
}

if (empty ( $_REQUEST ['code'] )) {
	display ( _er ( 'E_404' ), 0 );
}

global $cfg_renren;
import ( '/library/renrenoauth.php' );

$keys = array ();
$keys ['code'] = $_REQUEST ['code'];
$keys ['redirect_uri'] = callbackUrl ( 'renren' );
try {
	$o = new RenrenOAuth ( $cfg_renren ['key'], $cfg_renren ['secret'] );
	$token = $o->getAccessToken ( $_REQUEST ['code'], callbackUrl ( 'renren' ) );
	Encryption::serializeToFile ( $token, tmpDir ( 'renren.oauth' ) );
	lockit ( 'renren' );
	display ( 'renren认证成功' );
} catch ( OAuthException $e ) {
	display ( 'renren认证失败', 0 );
}
