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

$tpl = getTemplateInstance();

$tpl->locked_some = locked_some();

$tpl->display('install/auth');