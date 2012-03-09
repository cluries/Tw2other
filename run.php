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

define ( 'START_TIME', microtime ( true ) );
define ( 'BASE_PATH', dirname ( __FILE__ ) );
define ( 'DEBUG', true );

include BASE_PATH . '/config/config.inc.php';
include BASE_PATH . '/modules/init/bootstarp.php';

$targetSet = TargetSet::getInstance ();
$targets   = $targetSet->targets('targets');
 
// trigger_error("error_msg");
// throw new Exception("Error Processing Request", 1);

$twitter = new Twitter_Source ( $cfg_twitter );
$tweets = $twitter->gets ();

if (empty ( $tweets )) {
	display ( "Tw2other run well but there is no tweets need to sync" );
}
 
$modules = array('Sina','Tencent','Renren','Douban','Fanfou');
foreach ($modules as $m) {
	if (! file_exists(tmpDir(strtolower($m).'.oauth'))) {
		continue;
	}

	$target = "{$m}_Sync";
	$config = 'cfg_'.strtolower($m);
	$target = new $target($$config);
	$target->sets($tweets);
	$target->sync();
}

display('^_^');

?>