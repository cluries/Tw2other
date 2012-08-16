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



import('/library/saetv2.ex.class.php');

class Sina_Sync extends TargetBase
{	
	private $m_oauth;
	
	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'sina.oauth' ) );
		$this->m_oauth = new SaeTClientV2( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['access_token'] );
	}
	
	public function post($tweet)
	{
		$this->m_oauth->update($tweet);
	}
	
}
