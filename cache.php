<?php

use Pronto\ConfigContainer;

define('PRONTO_CACHE', PRONTO_ROOT.DS.'cache');

$hash = hash('sha256', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].print_r($_REQUEST, true).print_r($_FILES, true));
$file = PRONTO_CACHE.'/'.$hash.'.html';
$time = ConfigContainer::get('cache-time') ? ConfigContainer::get('cache-time') : 3600;

if (file_exists($file) && (time()-$time) < filemtime($file)) {
	readfile($file);
	if (ConfigContainer::get('cache-comment')) {
		echo "\n<!-- Cached @ ".date('r', filemtime($file))." -->";
	}
	exit;
}

ob_start();

register_shutdown_function(function() use ($file) {
	$buffer = ob_get_contents();
	$fp = fopen($file, 'w');
	fwrite($fp, $buffer);
	fclose($fp);
});

?>