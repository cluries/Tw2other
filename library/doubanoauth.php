<?php
 
class DoubanOAuth
{ 

	private  $m_signatureMethod ;

	private  $m_oauthConsumer;

	private  $m_token;


	const BASE_SERVICE_URL = "http://www.douban.com/service";

	const POST_URL = 'http://api.douban.com/miniblog/saying';

 
	public function requestTokenURL() 
	{ 
		return self::BASE_SERVICE_URL.'/auth/request_token';
	}

	function authorizeURL() 
	{ 
		return self::BASE_SERVICE_URL.'/auth/authorize'; 
	}

	function authenticateURL() 
	{ 
		return self::BASE_SERVICE_URL.'/auth/authenticate'; 
	}

	function accessTokenURL() 
	{ 
		return self::BASE_SERVICE_URL.'/auth/access_token'; 
	}

	 
	 
	public function __construct($key, $secret, $token = null, $tokenSecret = null)
	{
		$this->m_signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
		$this->m_oauthConsumer = new OAuthConsumer($key, $secret);

		if (!empty($token) && !empty($tokenSecret)) {
			$this->m_token = new OAuthConsumer($token, $tokenSecret);
		} else {
			$this->m_token = null;
		}
	}  


	public function torequest($url, $args = array(), $method = null, $posts = null) {
			
		$method = empty($method) ? (empty($args) ? "GET" : "POST") : $method;

		$request = OAuthRequest::from_consumer_and_token($this->m_oauthConsumer, $this->m_token, $method, $url, $args);
		$request->sign_request($this->m_signatureMethod, $this->m_oauthConsumer, $this->m_token);

		$response = null;

		switch ($method) {
		case 'GET': 
			$response = httpRequest($request->to_url());
			break;
		case 'POST':
			$response = $this->httpRequest($request->get_normalized_http_url(), $request->to_header(), $posts);
			break;
		}
  	
		return $response;
	}


	 
	public function getRequestToken() {
		$request = $this->torequest($this->requestTokenURL());
		$token 	 = $this->oAuthParseResponse($request);

		$this->m_token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}


	private function oAuthParseResponse($string) {
		$result = array();
		$params = explode('&', $string);
		foreach ($params as $param) {
			$pair = explode('=', $param, 2);

			if (count($pair) != 2) {
				continue;
			} 

			$result[urldecode($pair[0])] = urldecode($pair[1]);
		}

		return $result;
	}

	 
	function getAuthorizeURL($token, $callbackUrl) { 

		if (is_array($token)) {
			$token = $token['oauth_token'];
		}

		return $this->authorizeURL() . '?oauth_token=' . $token.'&oauth_callback=' . urlencode($callbackUrl);
	} 


	 
	function getAuthenticateURL($token)  {
		if (is_array($token)) {
			$token = $token['oauth_token'];
		}
			
		return $this->authenticateURL() . '?oauth_token=' . $token;
	}

	 
	function getAccessToken($token = null)  {
		$r = $this->torequest($this->accessTokenURL());
		$token = $this->oAuthParseResponse($r);
		$this->m_token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}  

	private function httpRequest($url, $header = null, $posts = null) { 

		$curlHandler = curl_init( $url);
		curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, 30);
	 
		if (!empty($header)) {
			$header = array('Content-Type: application/atom+xml',$header);
			curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $header);
		}
 
		

		if (!empty($posts)) {
			curl_setopt($curlHandler, CURLOPT_POST, 1);
			curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $posts);
		}

		$response = curl_exec($curlHandler);
		curl_close ($curlHandler);
 
		return $response;
	} 

	public function post($tweet)
	{

		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= '<entry xmlns:ns0="http://www.w3.org/2005/Atom" xmlns:db="http://www.douban.com/xmlns/">'."\n";
		$xml .= "<content>{$tweet}</content>\n</entry>";

	   	return $this->torequest(self::POST_URL, array( ), 'POST', $xml);
	}
	
}