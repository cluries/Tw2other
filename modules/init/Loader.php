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

function __get_real_clazz_name($clazz) {
    return preg_replace('/_/', '/', $clazz);
}

function __autoload($clazz) {
    $ext = ".php";
    $dirs = array(
        'helper' => BASE_PATH . '/helper',
        'init' => BASE_PATH . '/modules/init',
        'target' => BASE_PATH . '/modules/target',
        'source' => BASE_PATH . '/modules/source'
    );

    $clazz = __get_real_clazz_name($clazz);
    foreach($dirs as $name => $dir) {
        $clazz_file = $dir . "/" . $clazz . $ext;
        if (file_exists($clazz_file)) {
            include_once $clazz_file;
            return;
        }
    }
}

