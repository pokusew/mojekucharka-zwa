<?php

declare(strict_types=1);

namespace App\Mail;

use Core\Config;
use Core\Routing\Router;
use Nette\Mail\Message;

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
	 * @param array<string, mixed> $variables
	 * @return Message the generated message, do not forget to call setFrom before sending it
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

	public function getDefaultFrom(): string {
		return $this->config->parameters['email.from'];
	}

	public function getAdminEmail(): string {
		return $this->config->parameters['email.admin'];
	}

}
