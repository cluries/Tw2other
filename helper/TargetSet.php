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

class TargetSet
{
	private static $__static_instance__;

	private $m_target_file ;

	private $m_target_set  ;

	private function __construct()
	{
		$this->m_target_file = tmpDir('targets.targets');
		if (!file_exists($this->m_target_file)) {
			$this->m_target_set = array();
			return;
		}

		$this->m_target_set = Encryption::unserializeFromFile($this->m_target_file);
	} 

	public function __destruct()
	{
		Encryption::serializeToFile($this->m_target_set , $this->m_target_file);
	}

	public static function getInstance() 
	{
		if (empty(self::$__static_instance__)) {
			self::$__static_instance__ = new self();
		}

		return self::$__static_instance__;
	}

	public function targets($focus = null, $default = null)
	{
		return !empty( $focus ) ? ( isset( $this->m_target_set[$focus] ) ? $this->m_target_set[$focus] : $default ) : $this->m_target_set ;
	}

	public function set($key,$val = null)
	{
		if($val == null) {
			unset($this->m_target_set[$key]);
		}

		$this->m_target_set[$key] = $val;
	}

	public function get($key=null,$default = null) 
	{
		if ($key === null) {
			return $this->m_target_set;
		}

		return isset($this->m_target_set[$key]) ? $this->m_target_set[$key] : $default;
	}

}
