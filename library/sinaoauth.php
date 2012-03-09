<?php

class SinaOauth {
	
	private $_host = "http://api.t.sina.com.cn/";
	
	private $_sha1Method;
	
	private $_consumer;
	
	private $_token;
	
	public function accessTokenURL() {
		return 'http://api.t.sina.com.cn/oauth/access_token';
	}
	
	public function authenticateURL() {
		return 'http://api.t.sina.com.cn/oauth/authenticate';
	}
	
	public function authorizeURL() {
		return 'http://api.t.sina.com.cn/oauth/authorize';
	}
	
	public function requestTokenURL() {
		return 'http://api.t.sina.com.cn/oauth/request_token';
	}
	
	public function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
		$this->_sha1Method = new OAuthSignatureMethod_HMAC_SHA1 ();
		$this->_consumer = new OAuthConsumer ( $consumer_key, $consumer_secret );
		if (! empty ( $oauth_token ) && ! empty ( $oauth_token_secret )) {
			$this->_token = new OAuthConsumer ( $oauth_token, $oauth_token_secret );
		} else {
			$this->_token = NULL;
		}
	}
	
	public function getRequestToken($oauth_callback = NULL) {
		$parameters = array ();
		if (! empty ( $oauth_callback )) {
			$parameters ['oauth_callback'] = $oauth_callback;
		}
		
		$request = $this->oAuthRequest ( $this->requestTokenURL (), 'GET', $parameters );
		$token = OAuthUtil::parse_parameters ( $request );
		$this->_token = new OAuthConsumer ( $token ['oauth_token'], $token ['oauth_token_secret'] );
		return $token;
	}
	
	public function getAuthorizeURL($token, $back) {
		if (empty ( $token )) {
			echo '<strong>没鞥获取到TOKEN,以下操作可能会失败!</strong>';
		}
		if (is_array ( $token )) {
			$token = $token ['oauth_token'];
		}
		
		return $this->authorizeURL () . "?oauth_token={$token}&oauth_callback=" . urlencode ( $back );
	}
	
	public function getAccessToken($oauth_verifier = FALSE, $oauth_token = false) {
		$parameters = array ();
		if (! empty ( $oauth_verifier )) {
			$parameters ['oauth_verifier'] = $oauth_verifier;
		}
		
		$request = $this->oAuthRequest ( $this->accessTokenURL (), 'GET', $parameters );
		$token = OAuthUtil::parse_parameters ( $request );
		$this->_token = new OAuthConsumer ( $token ['oauth_token'], $token ['oauth_token_secret'] );
		return $token;
	}
	
	public function oAuthRequest($url, $method, $parameters) {
		
		if (strrpos ( $url, 'http://' ) !== 0 && strrpos ( $url, 'http://' ) !== 0) {
			$url = "{$this->_host}{$url}.{$this->format}";
		}
		
		$request = OAuthRequest::from_consumer_and_token ( $this->_consumer, $this->_token, $method, $url, $parameters );
		$request->sign_request ( $this->_sha1Method, $this->_consumer, $this->_token );
		
		switch ($method) {
			case 'GET' :
				return $this->httpRequest ( $request->to_url () );
			default :
				return $this->httpRequest ( $request->get_normalized_http_url (), $request->to_postdata () );
		}
	}
	
	private function httpRequest($url, $params = null, $decode = 0) {
		$curlHandle = curl_init ( $url );
		if (! empty ( $params )) {
			curl_setopt ( $curlHandle, CURLOPT_POST, true );
			if (is_array ( $params )) {
				$params = http_build_query ( $params );
			}
			
			echo $params;
			curl_setopt ( $curlHandle, CURLOPT_POSTFIELDS, $params );
		}
		
		curl_setopt ( $curlHandle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curlHandle, CURLOPT_TIMEOUT, 15 );
		curl_setopt ( $curlHandle, CURLOPT_ENCODING, 'UTF-8' );
		
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

}
?>