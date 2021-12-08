<?php

declare(strict_types=1);

require_once __DIR__ . '/built-in-web-server.php';

if (should_skip_this_request()) {
	// false indicates to the PHP built-in web server that it should handle the request itself
	return false;
}

require_once __DIR__ . '/autoload.php';

/** @var App\Config $config */
$config = require_once __DIR__ . '/../config/config.local.php';

Tracy\Debugger::$email = $config->debuggerEmail;
Tracy\Debugger::$logDirectory = $config->debuggerLogDirectory;
Tracy\Debugger::$productionMode = $config->isDevelopment() ? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION;
Tracy\Debugger::enable();

$container = new App\DI\Container();

$container->add($config);

$container->registerFactoryForType(
	'App\HttpRequest',
	[$container->getByType('App\HttpRequestFactory'), 'createHttpRequest'],
);

/** @var App\App $app */
$app = $container->getByType('App\App');

$app->run();
