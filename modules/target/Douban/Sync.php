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
import ( '/library/doubanoauth.php' );

class Douban_Sync extends TargetBase
{

	
	
	private $m_oauth;
	
	public function initOAuth()
	{
		$this->m_token = Encryption::unserializeFromFile ( tmpDir ( 'douban.oauth' ) );
		$this->m_oauth = new DoubanOAuth ( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['oauth_token'] ,$this->m_token['oauth_token_secret']  );
	}
	
	public function post($tweet)
	{
	    $this->m_oauth->post($tweet) ;
	}
	
 

}