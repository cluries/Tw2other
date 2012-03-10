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

import ( '/library/twitteroauth.php' );

class Twitter_Source extends SourceBase
{
	
	protected $m_source_type = 'twitter';
	
	private $m_connection;
	
	private function initOauth()
	{
		$this->m_connection = new TwitterOAuth ( $this->m_cfg ['key'], $this->m_cfg ['secret'], $this->m_token ['oauth_token'], $this->m_token ['oauth_token_secret'] );
	}
	
	private function getParameters($count = 50)
	{
		$parameters = array ();
		$parameters ['count'] = $count;
		$parameters ['include_rts'] = true;
		$parameters ['include_entities'] = true;
		
		if (! empty ( $this->m_info ['sinceId'] )) {
			$parameters ['since_id'] = $this->m_info ['sinceId'];
		}
 
		return $parameters;
	}
	
	public function gets()
	{
		if (! $this->isNeedRead ()) {
			display ( 'reflash too fast~', 0 );
			return;
		}
		
		$this->m_info ['last_time'] = time ();
		
		$this->initOauth ();
		$parameter = $this->getParameters ();
		$json = $this->m_connection->get ( 'statuses/user_timeline', $parameter );
		if (isset ( $json [0] ['id_str'] )) {
			$this->m_info ['sinceId'] = $json [0] ['id_str'];
		}
		
		$this->serializeInfo ();
		if (empty ( $json ) || ! is_array ( $json )) {
			display ( _er ( 'E_NO_UPDATE' ), 0 );
		}
		
		$result = array ();
		foreach ( $json as $tweet ) {
			$this->expandUrl ( $tweet );
			//array_push ( $result, $tweet ['text'] );
			$result ["{tweet_{$tweet['id_str']}}"] = trim ( $tweet ['text'] );
		}
		
		unset ( $json );
		$this->filter ( $result );
		
		return $result;
	}
	
	private function expandUrl(&$tweet)
	{
		if (empty ( $tweet ['entities'] ['urls'] )) {
			return;
		}
		
		foreach ( $tweet ['entities'] ['urls'] as $url ) {
			$tweet ['text'] = str_replace ( $url ['url'], $url ['expanded_url'], $tweet ['text'] );
		}
	}

}