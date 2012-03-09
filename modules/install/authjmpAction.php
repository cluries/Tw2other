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

$type = getVar ( 'type' );

if (islocked ( $type )) {
	display ( _er ( 'E_LOCKED', $type, $type ), 0 );
}

$callfunc = "{$type}JMP__";
if (! function_exists ( $callfunc )) {
	display ( _er ( 'E_404' ), 0 );
}

call_user_func ( $callfunc );

function twitterJMP__()
{
	import ( '/library/OAuth.php' );
	import ( '/library/twitteroauth.php' );
	
	$type = 'twitter';
	global $cfg_twitter;
	
	$connection = new TwitterOAuth ( $cfg_twitter ['key'], $cfg_twitter ['secret'] );
	$request_token = $connection->getRequestToken ( callbackUrl ( $type ) );
	
	$_SESSION ['oauth_token'] = $token = $request_token ['oauth_token'];
	$_SESSION ['oauth_token_secret'] = $request_token ['oauth_token_secret'];
	
	if ($connection->http_code == 200) {
		$url = $connection->getAuthorizeURL ( $token );
		header ( 'Location: ' . $url );
		break;
	} else {
		display ( 'Could not connect to Twitter. Refresh the page or try again later.', 0 );
	}
}

function sinaJMP__()
{
	import ( '/library/OAuth.php' );
	import ( '/library/sinaoauth.php' );
	
	global $cfg_sina;
	$type = 'sina';
	
	$callbackUrl = callbackUrl($type);

	$o 				= new SinaOauth ($cfg_sina['key'] , $cfg_sina['secret']);
	$token 			= $o->getRequestToken ( $callbackUrl );
	$authorizeURL 	= $o->getAuthorizeURL ( $token, $callbackUrl );

	$_SESSION ['sina_key'] = $token;

	//$o = new SaeTOAuthV2 ( $cfg_sina ['key'], $cfg_sina ['secret'] );
	//$authorizeURL = $o->getAuthorizeURL ( callbackUrl ( $type ) ,'code');
	

	header ( "Location:{$authorizeURL}" );
}

function tencentJMP__()
{
	import ( '/library/OAuth.php' );
	import ( '/library/tencentoauth.php' );
	
	global $cfg_tencent;
	$type = 'tencent';
	
	$o = new MBOpenTOAuth ( $cfg_tencent ['key'], $cfg_tencent ['secret'] );
	$keys = $o->getRequestToken ( callbackUrl ( $type ) );
	$authorizeURL = $o->getAuthorizeURL ( $keys ['oauth_token'], false, '' );
	$_SESSION ['tencent_keys'] = $keys;
	header ( "Location:{$authorizeURL}" );
}

function renrenJMP__()
{
	import ( '/library/renrenoauth.php' );
	global $cfg_renren;
	$type = 'renren';

	$o = new RenrenOAuth( $cfg_renren ['key'], $cfg_renren ['secret'] ); 
	$authorizeURL = $o->getAuthorizeURL (  $cfg_renren ['key'], 'status_update', callbackUrl ( $type )  );
	header ( "Location:{$authorizeURL}" );
}

function doubanJMP__()
{
	import ( '/library/OAuth.php' );
	import ( '/library/doubanoauth.php' );
	$type = 'douban';
	global $cfg_douban;

	$o = new DoubanOAuth($cfg_douban ['key'], $cfg_douban ['secret'] );
	$token =  $o->getRequestToken();
	
	$_SESSION['douban_token'] = $token['oauth_token'];
	$_SESSION['douban_token_secret'] = $token['oauth_token_secret'];

	$authorizeURL = $o->getAuthorizeURL($token,  callbackUrl ( $type ));
	header ( "Location:{$authorizeURL}" );
}

function fanfouJMP__()
{

}



