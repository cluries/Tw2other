<?php

class ConfigGenerator
{

	private $m_config_file;

	private $m_configed;

	private $m_configs;

	private $m_defined;

	private $m_defines;

	public function __construct() 
	{
		$this->m_config_file = BASE_PATH.'/config/config.inc.php';
		$this->m_configed = array();
		$this->m_defined  = array();
		if ( file_exists($this->m_config_file)) {
			$this->read();
		}
	}

	public function write( $filename = null) 
	{	
		$config = $this->mergeConfig();
		empty($filename) && $filename = $this->m_config_file;

	}

	private function read( $filename = null )
	{
		empty($filename) && $filename = $this->m_config_file;
		$readLineArr = file ( $filename );

		foreach ($readLineArr as &$line) {
			$line = trim($line);

		}
	}

	private function mergeConfig()
	{
		
	}
}