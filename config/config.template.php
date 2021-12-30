<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

use Core\Config;

function build_config(): Config
{

	$mode = Config::parseMode(getenv('MODE'), Config::DEVELOPMENT);
	$assetsMode = Config::parseMode(getenv('ASSETS_MODE'), $mode);

	$config = new Config();

	$config->mode = $mode;
	$config->debuggerEmail = null;
	$config->debuggerLogDirectory = __DIR__ . '/../log';

	$config->https = false;
	$config->host = "${_SERVER['SERVER_NAME']}:${_SERVER['SERVER_PORT']}";
	$config->basePath = '/';

	$config->assetsMode = $assetsMode;
	$config->assetsManifest = __DIR__ . "/../build/assets.$mode.json";
	$config->webpackDevServer = 'http://localhost:3000'; // only used if $config->isDevelopment() === true

	// fill in correct values for the database connection
	$config->databaseDsn = 'mysql:host=localhost;dbname=DB;user=USER;password=PASSWORD';

	return $config;

}

return build_config();
