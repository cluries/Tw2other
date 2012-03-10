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


import ( '/library/OAuth.php' );
import ( '/library/sinaoauth.php' );

class Sina_Sync extends TargetBase
{
	const POST_URL = 'http://api.t.sina.com.cn/statuses/update.json';
	
	private $m_oauth;
	
	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'sina.oauth' ) );
		$this->m_oauth = new SinaOauth ( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['oauth_token'], $this->m_token ['oauth_token_secret']  );
	}
	
	public function post($tweet)
	{
		$params = array ('status' => $tweet );
		echo $this->m_oauth->oAuthRequest ( self::POST_URL, 'POST', $params );
	}
	
}
