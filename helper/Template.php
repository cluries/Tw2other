<?php
/**
 * *********************************************************
 * 
 * @author cluries
 * @link http://cdbit.com
 * @package Core
 * @version 0.1
 * @copyright 2008 http://cdbit.com All rights reserved
 * 
 * *********************************************************
 */

class Template 
{
	
	protected $_templateDir = 'view';
	
	protected $_skin = 'default';
	
	protected $_caching = false;
	
	protected $_cacheDir = 'cache';
	
	protected $_cacheLifeTime = 500;
	
	protected $_suffix = 'phtml';
	
	protected $_vars = array ();
	
	public function assign($key, $val = '')
	{
		$this->_vars [$key] = $val;
	}
	
	public function __set($property, $val) 
	{
		$this->assign ( $property, $val );
	}
	
	public function __get($property) 
	{
		return isset ( $this->_vars [$property] ) ? $this->_vars [$property] : null;
	}
	
	public function get($property, $default = null) 
	{
		return isset ( $this->_vars [$property] ) ? $this->_vars [$property] : $default;
	}
	
	public function fetch($tpl) 
	{
		$tplFile = $this->tplFile ( $tpl );
		if (empty ( $tplFile )) {
			throw new Exception ( "File [{$tpl}] Not Found In Template Dir [$this->_templateDir]" );
			return;
		}
		ob_start ();
		ob_clean ();
		
		include $tplFile;
		$response = ob_get_contents ();
		ob_end_clean ();
		if ($this->_caching) {
			$cdir = $dir = $this->_cacheDir . '/' . Route::getModule () . '/' . $this->_skin;
			$tplArr = explode ( "/", $tpl );
			$tplArrCount = count ( $tplArr );
			if ($tplArrCount > 1) {
				for($i = 0; $i <= $tplArrCount - 2; $i ++) {
					$dir .= "/" . $tplArr [$i];
				}
			}
			if (! is_dir ( $dir )) {
				mkdir ( $dir, 0777, true );
			}
			$cacheFile = $cdir . '/' . $tpl . '.' . $this->_suffix;
			$fileHandler = fopen ( $cacheFile, 'w+' );
			fwrite ( $fileHandler, $this->createCacheHeader () . "\n" . $this->compressHtml ( $response ) );
			fclose ( $fileHandler );
		}

		return $response;
	}
	
	protected function render($file) 
	{
		$file = $this->tplFile ( $file );
		if (! empty ( $file )) {
			include $file;
		}
	}
	
	protected function importCss($file) 
	{
		return '<link type="text/css" rel="stylesheet" href="' . $file . '" />' . "\n";
	}
	
	protected function importScript($file) 
	{
		return '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
	}
	
	public function display($tpl) 
	{
		echo $this->fetch ( $tpl );
	}
	
	private function tplFile($tpl) 
	{
		$file = $this->_templateDir . '/' . $this->_skin . '/' . $tpl;
		$withSuffix = $file . '.' . $this->_suffix;
		if (file_exists ( $withSuffix )) {
			return $withSuffix;
		}
		
		if (file_exists ( $file )) {
			return $file;
		}
		
		return null;
	}
	
	private function cacheFileName($tpl) 
	{
	
	}
	
	private function cacheFile($tpl) 
	{
		$file = $this->_cacheDir . '/' . Route::getModule () . '/' . $this->_skin . '/' . $tpl;
		$withSuffix = $file . '.' . $this->_suffix;
		if (file_exists ( $withSuffix )) {
			return $withSuffix;
		}
		
		if (file_exists ( $file )) {
			return $file;
		}
		
		return null;
	}
	
	private function useCache($tpl) 
	{
		if (! $this->_caching) {
			return false;
		}
		$cacheFile = $this->cacheFile ( $tpl );
		if (empty ( $cacheFile )) {
			return false;
		}
		
		$mtime = filemtime ( $cacheFile );
		if ($mtime + $this->_cacheLifeTime < time ()) {
			return false;
		}
		
		return true;
	}
	
	private function createCacheHeader() 
	{
		return '<!-- Cache File . Created @ ' . date ( "Y-m-d H:i:s" ) . ' -->';
	}
	
	public function preCache($tpl) 
	{
		if ($this->useCache ( $tpl ) === true) {
			$cacheFile = $this->cacheFile ( $tpl );
			$response = file_get_contents ( $cacheFile );
			echo $response;
			flush ();
			exit ();
		}
	}
	
	private function compressHtml($html) 
	{
		$html = explode ( "\n", $html );
		foreach ( $html as &$line ) {
			$line = trim ( $line );
		}
		
		return implode ( "", $html );
	}
	
	public function setTemplateDir($templateDir) 
	{
		$templateDir = trim ( $templateDir );
		if (substr ( $templateDir, - 1 ) == '/') {
			$templateDir = substr ( $templateDir, 0, - 1 );
		}
		$this->_templateDir = $templateDir;
	}
	
	public function setSkin($skin) 
	{
		$this->_skin = $skin;
	}
	
	public function setCacheDir($cacheDir) 
	{
		$cacheDir = trim ( $cacheDir );
		if (substr ( $cacheDir, - 1 ) == '/') {
			$templateDir = substr ( $cacheDir, 0, - 1 );
		}
		$this->_cacheDir = $cacheDir;
	}
	
	public function setCaching($caching = false) 
	{
		$this->_caching = $caching;
	}
	
	public function setSuffix($suffix = 'html') 
	{
		$this->_suffix = trim ( $suffix );
	}
	
	public function setCacheLifeTime($time = 500)
	{
		$this->_cacheLifeTime = intval ( $time );
	}
	
	public function isCaching() 
	{
		return $this->_caching;
	}
	
	public function getCacheLifeTime() 
	{
		return $this->_cacheLifeTime;
	}

	public function getTemplateDir() 
	{
		return $this->_templateDir;
	}
	
	public function getSkin() 
	{
		return $this->_skin;
	}
	
	public function getCacheDir() 
	{
		return $this->_cacheDir;
	}
	
	public function getSuffix() 
	{
		return $this->_suffix;
	}

}

