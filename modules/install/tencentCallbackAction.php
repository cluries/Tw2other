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


if ( islocked('tencent') ) {
	display(_er('E_LOCKED','tencent','tencent'),0);
}
 
empty ( $_SESSION ['tencent_keys'] ) && display('SESSION中没找到对应的KEY',0);
 
import('/library/OAuth.php');
import('/library/tencentoauth.php');

global $cfg_tencent;

$o = new MBOpenTOAuth ( $cfg_tencent['key'], $cfg_tencent['secret'], $_SESSION ['tencent_keys'] ['oauth_token'], $_SESSION ['tencent_keys'] ['oauth_token_secret'] );
unset($_SESSION['tencent_keys']);
$last_key = $o->getAccessToken ( $_REQUEST ['oauth_verifier'] ); 
if (! empty ( $last_key ['oauth_token'] ) && ! empty ( $last_key ['oauth_token_secret'] )) {
	Encryption::serializeToFile($last_key , tmpDir('tencent.oauth'));
	lockit('tencent');
	display('Tencent认证成功');
} else {
	display('Tencent认证失败',0);
}
 
