<?php

// see https://www.php.net/manual/en/features.commandline.webserver.php
if (php_sapi_name() === 'php-cli') {
	return false;
}

$config = require_once __DIR__  . '/../config/config.local.php';

$assetsStr = file_get_contents($config['assetsManifest']);
$assets = json_decode($assetsStr, true);

// var_dump($assets);

require_once __DIR__ . '/templates/layout.php';
