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
	display ( _er ( 'E_LOCKED', 'sina', 'sina' ), 0 );
}
 
if (empty( $_SESSION ['sina_key'])) {
	display('SESSION中对应的KEY，请返回重试',0);
}

import ( '/library/OAuth.php' );
import ( '/library/sinaoauth.php' );

global $cfg_sina;

$o = new SinaOauth ( $cfg_sina['key'], $cfg_sina['secret'], $_SESSION ['sina_key'] ['oauth_token'], $_SESSION ['sina_key'] ['oauth_token_secret'] );
unset ( $_SESSION ['sina_key'] );
$token = $o->getAccessToken ( $_REQUEST ['oauth_verifier'] );
if (! empty ( $token ['oauth_token'] ) && ! empty ( $token ['oauth_token_secret'] )) {
	Encryption::serializeToFile ( $token, tmpDir ( 'sina.oauth' ) );
	lockit ( 'sina' );
	display ( 'sina认证成功' );
}  

display ( 'sina认证失败' );