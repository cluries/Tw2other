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


if (islocked ( 'douban' )) {
	display ( _er ( 'E_LOCKED', 'douban', 'douban' ), 0 );
}

if (empty ( $_REQUEST ['oauth_token'] )) {
	display ( _er ( 'E_404' ), 0 );
}

if (  $_SESSION ['douban_token'] != $_REQUEST ['oauth_token']) {
	retry();
}


 
import ( '/library/OAuth.php' );
import ( '/library/doubanoauth.php' );

global $cfg_douban;

$o = new DoubanOAuth($cfg_douban['key'] , $cfg_douban['secret'],$_SESSION['douban_token'],$_SESSION['douban_token_secret']);
$token = $o->getAccessToken();

if (!empty($token['oauth_token_secret']) && !empty($token['oauth_token'])) {
	Encryption::serializeToFile ( $token, tmpDir ( 'douban.oauth' ) );
 
	lockit ( 'douban' );
	display ( 'douban认证成功' );
}

display ( 'douban认证失败' );

function retry()
{
	Header("Location:install.php?action=auth");
}
 