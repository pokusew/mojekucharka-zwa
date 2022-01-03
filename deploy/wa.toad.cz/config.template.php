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
	 * TODO: add see tag
	 */
	// fill in correct values for the database connection
	$config->parameters['databaseDsn'] = 'mysql:host=localhost;dbname=DB;user=USER;password=PASSWORD';

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
