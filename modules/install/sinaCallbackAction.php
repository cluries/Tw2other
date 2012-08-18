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

if (islocked ( 'sina' )) {
	$takedToken = Encryption::unserializeFromFile ( tmpDir ( 'sina.oauth' ) );
	if (! ($takedToken ['auth_timestamp'] + $takedToken ['expires_in'] < time () - 10)) {
		
		display ( _er ( 'E_LOCKED', 'sina', 'sina' ), 0 );
	}
}

import ( '/library/saetv2.ex.class.php' );

global $cfg_sina;
$o = new SaeTOAuthV2 ( $cfg_sina ['key'], $cfg_sina ['secret'] );

if (! isset ( $_REQUEST ['code'] )) {
	display ( 'sina认证失败' );
}

$keys = array ();
$keys ['code'] = $_REQUEST ['code'];
$keys ['redirect_uri'] = callbackUrl ( 'sina' );
$token = false;
try {
	$token = $o->getAccessToken ( 'code', $keys );
} catch ( OAuthException $e ) {
	display ( 'sina认证失败:' . $e->getMessage () );
}

if ($token) {
	$token ['auth_timestamp'] = time ();
	Encryption::serializeToFile ( $token, tmpDir ( 'sina.oauth' ) );
	lockit ( 'sina' );
	display ( 'sina认证成功' );
}
