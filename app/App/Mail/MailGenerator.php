<?php

declare(strict_types=1);

namespace App\Mail;

use Core\Config;
use Core\Routing\Router;
use Nette\Mail\Message;

/**
 * A service for generating app-specific e-mail messages from templates.
 */
class MailGenerator
{

	private Config $config;
	private Router $router;

	public function __construct(Config $config, Router $router)
	{
		$this->config = $config;
		$this->router = $router;
	}

	/**
	 * Creates a new e-mail message from the given template.
	 * @param string $templateName
	 * @param array<string, mixed> $variables template specific variables, name => value
	 * @return Message The generated message, without any recipient set. Do not forget
	 *                 to add least one recipient using {@see Message::addTo()} before sending it.
	 */
	public function createFromTemplate(string $templateName, array $variables): Message
	{
		// default variables available in every template
		$config = $this->config;
		$router = $this->router;
		$subject = null;

		// template specific variables
		extract($variables);

		ob_start();
		require __DIR__ . '/templates/' . $templateName . '.php';
		$html = ob_get_clean();

		$mail = new Message();
		$mail->setFrom($this->getDefaultFrom());

		// $subject can be changed in the required template
		// @phpstan-ignore-next-line
		if ($subject !== null) {
			$mail->setSubject($subject);
		}

		$mail->setHtmlBody($html);

		return $mail;
	}

	/**
	 * Returns the default From value (`email.from` config parameter).
	 */
	public function getDefaultFrom(): string
	{
		return $this->config->parameters['email.from'];
	}

	/**
	 * Returns the admin e-mail (`email.admin` config parameter).
	 */
	public function getAdminEmail(): string
	{
		return $this->config->parameters['email.admin'];
	}

}
