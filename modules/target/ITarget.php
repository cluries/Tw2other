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

interface ITarget
{

	public function initOAuth();

	public function sets($tweets);

	public function sync();

	public function post($tweet);

	public function filter();

	public function addFilter($callback);
}