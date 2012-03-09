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

class RenrenOAuth 
{
	const ACCESS_TOKEN_URL = 'http://graph.renren.com/oauth/token';

	const AUTHORIZEU_RL    = 'http://graph.renren.com/oauth/authorize';

	private $m_key;

	private $m_secret;

	public function __construct($key,$secret)
	{
		$this->m_key 	= trim($key);
		$this->m_secret = trim($secret);
	}

	public function getAccessToken($authorizationCode, $callbackUrl)
	{
		$request = array
		(
		    "redirect_uri"  => $callbackUrl,
		    "grant_type" 	=> 'authorization_code',
		    "code"		    => $authorizationCode,
		    "client_id"  	=> $this->m_key,
		    "client_secret" => $this->m_secret,
		);

		return httpRequest ( self::ACCESS_TOKEN_URL , $request, 2 );
	}

	public function getRefreshToken($refreshToken) 
	{
		$request = array 
		(
		    "refresh_token" => $refreshToken,
		    "grant_type" 	=> 'refresh_token',
		    "client_id"  	=> $this->m_key,
		    "client_secret" => $this->m_secret,
	   	);

		return httpRequest ( $this->accessTokenURL (), $request, 2 );
	}

 
	 

	public function getAuthorizeURL($consumerKey, $scope, $callbackUrl) {		
		return  self::AUTHORIZEU_RL . "?response_type=code&scope={$scope}&client_id={$consumerKey}&redirect_uri=" . urlencode ( $callbackUrl );
	}

}
?>