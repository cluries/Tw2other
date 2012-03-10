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

abstract class SourceBase implements ISource
{
	protected $m_cfg;
	
	protected $m_token;
	
	protected $m_info = array ();
	
	protected $m_filters;
	
	protected $m_after_filters;
	
	protected $m_info_file;
	
	protected $m_token_file;
	
	protected $m_source_type = 'source';
	
	protected $m_force_sync = array ();
	
	//	protected $m_force_nsync = array ();
	

	public function __construct($cfg)
	{
		$this->m_cfg = $cfg;
		$this->m_filters = array (array ($this, 'ftForceSync' ), array ($this, 'ftForceNSync' ), array ($this, 'ftLevel' ) );
		$this->m_after_filters = array (/*array ($this, 'aftForceSync' )*/ );
		
		$this->m_info_file = tmpDir ( $this->m_source_type . '.info' );
		$this->m_token_file = tmpDir ( $this->m_source_type . '.oauth' );
		
		if (! file_exists ( $this->m_token_file )) {
			throw new TOException ( _er ( 'E_SOURCE_FILE_NOT_EXISTS', $this->m_info_file, $this->m_token_file ) );
			return;
		}
		
		if (file_exists ( $this->m_info_file )) {
			//$this->m_info  = Encryption::unserializeFromFile  ( $this->m_info_file );
			$this->m_info = unserialize ( file_get_contents ( $this->m_info_file ) );
		}
		
		$this->m_token = Encryption::unserializeFromFile ( $this->m_token_file );
	}
	
	public function isNeedRead()
	{
		return time () - $this->m_info ['last_time'] > $this->m_cfg ['interval'];
	}
	
	public function addFilter($callback)
	{
		array_push ( $this->m_filters, $callback );
	}

	public function addAfterFilter($callback) 
	{
		array_push( $this->m_after_filters, $callback);
	}
	
	public function filter(&$sources)
	{
		if ( is_array($this->m_filters) && !empty($this->m_filters) ) {
			foreach ( $this->m_filters as $callback ) {
				if (is_string ( $callback ) || is_array ( $callback )) {
					$sources = call_user_func ( $callback, $sources );
				}
			}
		}
		
		if ( is_array($this->m_after_filters) && !empty($this->m_after_filters) ) {
			foreach ( $this->m_after_filters as $callback ) {
				if (is_string ( $callback ) || is_array ( $callback )) {
					$sources = call_user_func ( $callback, $sources );
				}
			}
		}	
		
		return $sources;
	}
	
	public function setToken($token, $type = null)
	{
		if (empty ( $type )) {
			$this->m_token = $token;
			return;
		}
		
		$this->m_token [$type] = $token;
	}
	
	public function setInfo($info, $type = null)
	{
		if (empty ( $info )) {
			$this->m_info = $info;
			return;
		}
		
		$this->m_info [$type] = $info;
	}
	
	protected function serializeInfo()
	{
		//Encryption::serializeToFile($this->m_info , $this->m_info_file);
		file_put_contents ( $this->m_info_file, serialize ( $this->m_info ) );
	}
	
	protected function ftForceSync($sources)
	{
		if (empty ( $this->m_cfg ['tagToSync'] )) {
			return $sources;
		}
		
		foreach ( $sources as $tweetId => $tweet ) {
			if (false !== strpos ( $tweet, $this->m_cfg ['tagNotSync'] )) {
				array_push ( $this->m_force_sync, $tweetId );
			}
		}
		
		return $sources;
	}
	
	protected function ftForceNSync($sources)
	{
		if (empty ( $this->m_cfg ['tagNotSync'] )) {
			return $sources;
		}
		
		foreach ( $sources as $tweetId => $tweet ) {
			if (false !== strpos ( $tweet, $this->m_cfg ['tagNotSync'] )) {
				unset ( $content [$k] );
			}
		}
		
		return $sources;
	}
	
	protected function ftLevel($sources)
	{
		$pattern = null;
		switch ($this->m_cfg['level']) {
		case 1 :
			$pattern = '/^@.*/m';
			break;
		case 2 :
			$pattern = '/^RT\s@.*/m';
			break;
		case 3 :
			$pattern = '/RT\s|@/';
			break;
		case 4 :
			$pattern = '/.*/';
		default :
			return $sources;
		}
		
		if (! is_array($this->m_force_sync)) {
			$this->m_force_sync = array();
		}

		foreach ( $sources as $tweetId => $tweet ) {
			if ( ! in_array($tweetId , $this->m_force_sync) && preg_match ( $pattern, $tweet )) {
				unset ( $sources [$tweetId] );
			}
		}
		
		return $sources;
	}
	
	protected function aftForceSync($sources)
	{
		return $sources;
	}

}