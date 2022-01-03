<?php

declare(strict_types=1);

require_once __DIR__ . '/built-in-web-server.php';

if (should_skip_this_request()) {
	// false indicates to the PHP built-in web server that it should handle the request itself
	return false;
}

require_once __DIR__ . '/autoload.php';

/** @var Core\Config $config */
$config = require_once __DIR__ . '/../config/config.local.php';

Tracy\Debugger::$strictMode = true;
Tracy\Debugger::$email = $config->debuggerEmail;
Tracy\Debugger::$logDirectory = $config->debuggerLogDirectory;
Tracy\Debugger::$productionMode = $config->isModeDevelopment()
	? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION;
Tracy\Debugger::enable();

Nette\Utils\Html::$xhtml = true;

$container = new Core\DI\Container($config);

Tracy\Debugger::getBar()->addPanel(new Core\DI\ContainerTracyPanel($container));

/** @var Core\App $app */
$app = $container->getByType(Core\App::class);

$app->run();
