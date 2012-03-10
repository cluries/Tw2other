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


import('/library/OAuth.php');
import('/library/twitteroauth.php');

if ( islocked('twitter') ) {
	display(_er('E_LOCKED','twitter','twitter'),0);
}

if (isset ( $_REQUEST ['oauth_token'] ) && $_SESSION ['oauth_token'] != $_REQUEST ['oauth_token']) {
	retry();
}

global $cfg_source;

$connection = new TwitterOAuth ( $cfg_source['key'] ,$cfg_source['secret'], $_SESSION ['oauth_token'], $_SESSION ['oauth_token_secret'] );
$access_token = $connection->getAccessToken ( $_REQUEST ['oauth_verifier'] );
$_SESSION ['access_token'] = $access_token;
unset ( $_SESSION ['oauth_token'] );
unset ( $_SESSION ['oauth_token_secret'] );

if (200 == $connection->http_code) {
	$_SESSION ['status'] = 'verified';
	//updateOauth ( $access_token );
	Encryption::serializeToFile($access_token , tmpDir('twitter.oauth'));
	lockit('twitter');
	display('Twiter认证成功');
} else {
	retry();
}

function retry()
{
	Header("Location:install.php?action=auth");
}