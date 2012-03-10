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

interface ISource
{
	public function setInfo($info, $type = null);
	
	public function setToken($token, $type = null);
	
	public function isNeedRead();
	
	public function addFilter($callback);

	public function addAfterFilter($callback);
	
	public function filter(&$sources);
	
	public function gets();
}