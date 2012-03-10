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



abstract class TargetBase implements ITarget
{

	protected $m_tweets  = array();

	protected $m_filters = array();

	protected $m_token   = array();

	protected $m_cfg 	 = array();

	public function __construct($cfg)
	{
		$this->m_cfg	 = $cfg;
		$this->m_filters = array ( array ($this, 'ftSpecialTag' ) );
	}
	
	public function sets($tweets) 
	{
		$this->m_tweets	= $tweets;
	}

	public function sync() 
	{
		if (!is_array($this->m_tweets) || empty($this->m_tweets)) {
			return;
		}

		$this->filter();

		if(empty($this->m_tweets)) {
			return;
		}

		$this->initOAuth();
		foreach ($this->m_tweets as $tweet) {
			$this->post(trim($tweet));
		}
	}

	public function addFilter($callback)
	{
		array_push ( $this->m_filters, $callback );
	}
	
	public function filter()
	{
		foreach ( $this->m_filters as $callback ) {
			if (is_string ( $callback ) || is_array ( $callback )) {
				$this->m_tweets = call_user_func ( $callback,$this->m_tweets );
			}
		}
		
		return $this->m_tweets;
	}

	protected function ftSpecialTag($sources)
	{
		if (empty($this->m_cfg['specialTag'])) {
			return $sources;
		}

		foreach ($sources as $tweetId => $tweet) {
			if (false !== strpos ( $tweet, $this->m_cfg ['specialTag'] )) {
				unset($sources[$tweetId]);
			}
		}

		return $sources;
	}
}
