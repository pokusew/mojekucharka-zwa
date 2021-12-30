<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

use Core\Config;

function build_config(): Config
{

	$config = new Config();

	$config->mode = Config::PRODUCTION;
	$config->debuggerEmail = null;
	$config->debuggerLogDirectory = __DIR__ . '/../log';

	$config->https = true;
	$config->host = 'wa.toad.cz';
	$config->basePath = '/~endlemar/';

	$config->assetsMode = Config::PRODUCTION;
	$config->assetsManifest = __DIR__ . '/assets.wa.toad.cz.json';
	$config->webpackDevServer = null; // only used if $config->isDevelopment() === true

	// fill in the correct PASSWORD for the database connection
	$config->databaseDsn = 'mysql:host=localhost;dbname=endlemar;user=endlemar;password=PASSWORD';

	return $config;

}

return build_config();
