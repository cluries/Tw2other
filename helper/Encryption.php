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

!defined('ENCRYPT_SECRET') && exit();

class Encryption
{

	public static function encrypt($string,$secret = null) 
	{
		$secret = empty($secret) ? ENCRYPT_SECRET : $secret;
		$ivsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv 	= self::generateIV ($ivsize , $secret);
		return mcrypt_cbc ( MCRYPT_RIJNDAEL_128, $secret, $string, MCRYPT_MODE_CBC, $iv  ) ;
	}

	public static function decrypt($string,$secret = null) 
	{
		$secret = empty($secret) ? ENCRYPT_SECRET : $secret;
		$ivsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv 	= self::generateIV ($ivsize , $secret);
		return mcrypt_decrypt ( MCRYPT_RIJNDAEL_128, $secret, $string, MCRYPT_MODE_CBC, $iv  ) ;
	}

	public static function encryptFile($filename,$secret = null) 
	{
		file_put_contents($filename, self::encrypt(file_get_contents($filename),$secret));
	}

	public static function decryptFilecontent($filename,$secret = null) 
	{
		if (!file_exists($filename)) {
			throw new TOException(_er("E_FILE_NOT_EXISTS",$filename));
		}

		return self::decrypt(file_get_contents($filename),$secret);
	}

	public static function serializeToFile($var,$filename,$secret = null) 
	{
		file_put_contents($filename, self::encrypt(serialize($var), $secret));
	}

	public static function unserializeFromFile($filename,$secret = null) 
	{
		if (!file_exists($filename)) {
			throw new TOException(_er("E_FILE_NOT_EXISTS",$filename));
		}

		return unserialize(self::decrypt(file_get_contents($filename),$secret));
	}

	private static function generateIV($size,$secret = null) 
	{
		$secret = empty($secret) ? ENCRYPT_SECRET : $secret;
		$iv 	= '';

		if (($sub = (strlen($secret) - $size)) < 0) {
			$secret = str_repeat($secret, (int)(abs($sub)/strlen($secret))+2);
		}

		$secret = str_repeat($secret, 2);
		return substr($secret, 0,$size/2).substr($secret, 0-($size/2));
	}
	
}
