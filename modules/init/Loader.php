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

class Loader
{
	
	protected static $m_instance = null;
	
	private function __construct()
	{
	    /*** nullify any existing autoloads ***/
        spl_autoload_register(null, false);

        /*** specify extensions that may be loaded ***/
        spl_autoload_extensions('.php');
        
		spl_autoload_register ( array ($this, 'init' ) );
		spl_autoload_register ( array ($this, 'helper' ) );
		spl_autoload_register ( array ($this, 'target' ) );
		spl_autoload_register ( array ($this, 'source' ) );
	}
	
	public static function getInstance()
	{
		if (empty ( self::$m_instance )) {
			self::$m_instance = new Loader ();
		}
		
		return self::$m_instance;
	}
	
	protected function init($clazz)
	{
		$dir = BASE_PATH . '/modules/init';
		$file = "$dir/$clazz.php";
		if (!file_exists($file))
        {
            return false;
        }
        include $file;
	}

	protected function helper($clazz)
	{
		$dir = BASE_PATH . '/helper';
		$this->splitClazz( $clazz , $dir);
		$file = "$dir/$clazz.php";
		if (!file_exists($file))
        {
            return false;
        }
        include $file;
	}

	protected function target($clazz) 
	{
		$dir = BASE_PATH. '/modules/target';
		$this->splitClazz($clazz,$dir);	 
		$file = "$dir/$clazz.php";
		if (!file_exists($file))
        {
            return false;
        }
        include $file;
	}

	protected function source($clazz) 
	{
		$dir = BASE_PATH. '/modules/source';
		$this->splitClazz($clazz,$dir);	 
		$file = "$dir/$clazz.php";
		if (!file_exists($file))
        {
            return false;
        }
        include $file;
	}

	protected function splitClazz(& $clazz, &$baseDir)
	{
		$clazzSplits = explode('_', $clazz);
		if(count($clazzSplits) > 1) {
			$i = 0;
			while (isset($clazzSplits[$i+1])) {
				$baseDir .= "/{$clazzSplits[$i++]}";
			}

			$clazz = $clazzSplits[$i];
		}
	}

}

?>