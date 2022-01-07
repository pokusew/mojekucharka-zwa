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

	/**
	 * Register the application router
	 * @see \App\RouterFactory::createRouter
	 */
	$config->factories[] = 'App\RouterFactory::createRouter';

	/**
	 * Assets configuration
	 * @see \Core\Assets
	 */
	$config->parameters['assets.assetsMode'] = Config::PRODUCTION;
	$config->parameters['assets.assetsManifest'] = __DIR__ . '/assets.wa.toad.cz.json';
	$config->parameters['assets.webpackDevServerUrl'] = null;

	/**
	 * Database configuration
	 * @see \PDO
	 */
	// fill in correct values for the database connection
	$config->parameters['PDO.dsn'] = 'mysql:host=localhost;charset=utf8mb4;dbname=DB;user=USER;password=PASSWORD';
	// :START \ReflectionParameter::isDefaultValueAvailable() on the optional parameters of \PDO::__construct() class
	//        in PHP 7.4 on wa.toad.cz (incorrectly?) returns `false`
	//        so we have to explicitly pass them in order for the DI Container to be able to instantiate it
	$config->parameters['PDO.username'] = null;
	$config->parameters['PDO.passwd'] = null;
	// :END
	$config->parameters['PDO.options'] = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		// see https://stackoverflow.com/questions/20079320/how-do-i-return-integer-and-numeric-columns-from-mysql-as-integers-and-numerics
		PDO::ATTR_EMULATE_PREPARES => false,
		PDO::ATTR_STRINGIFY_FETCHES => false,
	];

	/**
	 * SMTP mailer configuration
	 * @see \Nette\Mail\SmtpMailer
	 * @see https://doc.nette.org/en/mail#toc-smtpmailer
	 */
	$config->services[] = 'Nette\Mail\SmtpMailer';
	$config->parameters['email.from'] = '"Mojekuchařka.net" <info@mojekucharka.net>';
	$config->parameters['email.admin'] = 'admin@example.com'; // fill in e-mail address for notifications
	$config->parameters['Nette\Mail\SmtpMailer.options'] = [
		// fill in correct config
	];

	return $config;

}

return build_config();
