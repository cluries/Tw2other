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

import ( '/library/renrenoauth.php' );

class Renren_Sync extends TargetBase
{

	private $m_oauth;

	const POST_URL = 'http://api.renren.com/restserver.do';
	
	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'renren.oauth' ) );
		//var_dump($this->m_token);
		$this->m_oauth = new RenrenOAuth($this->m_cfg['key'] , $this->m_cfg['secret']);
		 
	}

	private function  generatePostParams($tweet)
	{
		$params = array
		(
			'format'		=>	'JSON',
			'method'		=>	'status.set',
			'v'				=>	'1.0'
		);

		$params['access_token'] = $this->m_token['access_token'];
		$params['status'] 		= $tweet;
		
		ksort($params);
		reset($params);

		$before = '';
		foreach($params AS $k=>$v){
			$before .= $k.'='.$v;
		}
 
 		//exit($before.$this->m_cfg['secret']);
		$params['sig'] =  md5($before.$this->m_cfg['secret']);

		return $params;
	}
	
	public function post($tweet)
	{
		$response = httpRequest ( self::POST_URL, $this->generatePostParams($tweet) );
		$token = $this->m_oauth->getRefreshToken($this->m_token['refresh_token']);
		if (! empty ( $token ['access_token'] ) && ! empty ( $token ['refresh_token'] )) {
			Encryption::serializeToFile($token,tmpDir('renren.oauth')); 
		}
	}
}