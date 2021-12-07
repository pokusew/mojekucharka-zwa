<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

/** @var App\Config $config */
$config = require_once __DIR__ . '/../config/config.local.php';

Tracy\Debugger::$email = $config->debuggerEmail;
Tracy\Debugger::$logDirectory = $config->debuggerLogDirectory;
Tracy\Debugger::$productionMode = $config->isDevelopment() ? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION;
Tracy\Debugger::enable();

// see https://www.php.net/manual/en/features.commandline.webserver.php
// if (php_sapi_name() === 'cli-server') {
// 	// var_dump('x');
// 	return false;
// }

$assets = new App\Assets($config);
$router = new App\Router($config);

require_once __DIR__ . '/templates/layout.php';

// $request = \App\HttpRequest::fromSuperglobals();
// dump($request);
// dump($_GET);
