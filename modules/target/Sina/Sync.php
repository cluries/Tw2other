<?php
/**
 * *********************************************************
 * 
 * @author cluries
 * @link http://cdbit.com
 * @version 1.0
 * @copyright 2012 http://cdbit.com All rights reserved
 * 
 *  
 * 
 * *********************************************************
 */

import ( '/library/saetv2.ex.class.php' );


class Sina_Sync extends TargetBase
{
	private $m_oauth;
	private $m_reflash_cookie;


	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'sina.oauth' ) );
		
		if ($this->m_token ['auth_timestamp'] + $this->m_token ['expires_in'] < time () - 10) {
			$this->reflashToken ();
			$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'sina.oauth' ) );
		}
		
		$this->m_oauth = new SaeTClientV2 ( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['access_token'] );
	}


	public function post($tweet)
	{
		$this->m_oauth->update ( $tweet );
	}


	private function reflashToken()
	{
		$this->m_reflash_cookie = tmpDir ( 'reflashsina.cookie' );
		if (! file_exists ( $this->m_reflash_cookie )) {
			touch ( $this->m_reflash_cookie );
		}
		
		$loginResult = $this->curlLoginSina ( $this->m_cfg ['username'], $this->m_cfg ['password'] );
		
		if (! $loginResult) {
			return $loginResult;
		}
		
		global $cfg_sina;
		$callbackUrl = callbackUrl ( 'sina' );
		$o = new SaeTOAuthV2 ( $cfg_sina ['key'], $cfg_sina ['secret'] );
		$authorizeURL = $o->getAuthorizeURL ( $callbackUrl );
		
		$ch = curl_init ( $authorizeURL );
		$option = array ();
		$option [CURLOPT_FOLLOWLOCATION] = 1;
		$option [CURLOPT_RETURNTRANSFER] = 1;
		$option [CURLOPT_COOKIEJAR] = $this->m_reflash_cookie;
		$option [CURLOPT_COOKIEFILE] = $this->m_reflash_cookie;
		$option [CURLOPT_HTTPHEADER] = array (
				'Accept-Language: zh-cn',
				'Connection: Keep-Alive',
				'Cache-Control: no-cache' 
		);
		$option [CURLOPT_USERAGENT] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		curl_setopt_array ( $ch, $option );
		curl_exec ( $ch );
		curl_close ( $ch );
		unlink ( $this->m_reflash_cookie );
	}


	private function curlRequest($url, $data = '')
	{
		$ch = curl_init ();
		$option = array (
				CURLOPT_URL => $url,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1 
		);
		
		$option [CURLOPT_HTTPHEADER] = array (
				'Accept-Language: zh-cn',
				'Connection: Keep-Alive',
				'Cache-Control: no-cache' 
		);
		$option [CURLOPT_USERAGENT] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		
		if ($this->m_reflash_cookie) {
			$option [CURLOPT_COOKIEJAR] = $this->m_reflash_cookie;
			$option [CURLOPT_COOKIEFILE] = $this->m_reflash_cookie;
		}
		
		if ($data) {
			$option [CURLOPT_POST] = 1;
			$option [CURLOPT_POSTFIELDS] = $data;
		}
		
		curl_setopt_array ( $ch, $option );
		$response = curl_exec ( $ch );
		if (curl_errno ( $ch ) > 0) {
			exit ( "CURL ERROR:$url " . curl_error ( $ch ) );
		}
		curl_close ( $ch );
		return $response;
	}


	private function curlLoginSina($username, $password)
	{
		$preLoginData = $this->curlRequest ( 'http://login.sina.com.cn/sso/prelogin.php?entry=weibo&callback=sinaSSOController.preloginCallBack&su=' . base64_encode ( $username ) . '&client=ssologin.js(v1.3.16)', '' );
		preg_match ( '/sinaSSOController.preloginCallBack\((.*)\)/', $preLoginData, $preArr );
		$jsonArr = json_decode ( $preArr [1], true );
		
		if (! is_array ( $jsonArr )) {
			return false;
		}
		
		$postArr = array (
				'entry' => 'weibo',
				'gateway' => 1,
				'from' => '',
				'vsnval' => '',
				'savestate' => 7,
				'useticket' => 1,
				'ssosimplelogin' => 1,
				'su' => base64_encode ( urlencode ( $username ) ),
				'service' => 'miniblog',
				'servertime' => $jsonArr ['servertime'],
				'nonce' => $jsonArr ['nonce'],
				'pwencode' => 'wsse',
				'sp' => sha1 ( sha1 ( sha1 ( $password ) ) . $jsonArr ['servertime'] . $jsonArr ['nonce'] ),
				'encoding' => 'UTF-8',
				'url' => 'http://weibo.com/ajaxlogin.php?framelogin=1&callback=parent.sinaSSOController.feedBackUrlCallBack',
				'returntype' => 'META' 
		);
		
		$loginData = $this->curlRequest ( 'http://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.3.19)', $postArr );
		
		if (! $loginData) {
			return false;
		}
		
		$matchs = array ();
		
		preg_match ( '/replace\(\'(.*?)\'\)/', $loginData, $matchs );
		$loginResult = $this->curlRequest ( $matchs [1], '' );
		
		$loginResultArr = array ();
		preg_match ( '/feedBackUrlCallBack\((.*?)\)/', $loginResult, $loginResultArr );
		$userInfo = json_decode ( $loginResultArr [1], true );
		
		return ! empty ( $userInfo );
	}
}



