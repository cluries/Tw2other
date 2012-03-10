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
import ( '/library/tencentoauth.php' );

class Tencent_Sync extends TargetBase
{
	
	private $m_oauth;
	
	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'tencent.oauth' ) );
		$this->m_oauth = new MBOpenTOAuth ( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['oauth_token'], $this->m_token ['oauth_token_secret'] );
	}
	
	public function post($tweet)
	{
		$params = array ('format' => 'json', 'content' => $tweet, 'clientip' => $_SERVER ['REMOTE_ADDR'], 'jing' => '', 'wei' => '' );
		$this->m_oauth->post ( 'http://open.t.qq.com/api/t/add?f=1', $params );
	}
}