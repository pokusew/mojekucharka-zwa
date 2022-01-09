<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Exceptions\InvalidStateException;
use RuntimeException;

/**
 * A simple wrapper around PHP's native session
 * that sets secure defaults.
 */
class Session
{

	/** Default file lifetime (session.gc_maxlifetime) */
	private const DEFAULT_FILE_LIFETIME = 3 * 3600; // 3 hours in seconds

	private const SECURITY_OPTIONS = [
		'referer_check' => '',     // must be disabled because PHP implementation is invalid
		'use_cookies' => 1,        // must be enabled to prevent Session Hijacking and Fixation
		'use_only_cookies' => 1,   // must be enabled to prevent Session Fixation
		'use_trans_sid' => 0,      // must be disabled to prevent Session Hijacking and Fixation
		'use_strict_mode' => 1,    // must be enabled to prevent Session Fixation
		'cookie_httponly' => true, // must be enabled to prevent Session Hijacking
	];

	/** @var mixed[] */
	public array $options = [
		'cookie_domain' => '',
		'cookie_path' => '/',
		'cookie_secure' => true,
		'cookie_samesite' => HttpResponse::SAME_SITE_LAX,
		'cookie_lifetime' => 0, // for a maximum of 3 hours or until the browser is closed
		'gc_maxlifetime' => self::DEFAULT_FILE_LIFETIME, // 3 hours
	];

	private bool $regenerated = false;

	private bool $started = false;

	private HttpResponse $httpResponse;

	public function __construct(HttpResponse $httpResponse)
	{
		$this->httpResponse = $httpResponse;
		$this->options['cookie_domain'] = &$httpResponse->cookieDomain;
		$this->options['cookie_path'] = &$httpResponse->cookiePath;
		$this->options['cookie_secure'] = &$httpResponse->cookieSecure;
		$this->options['cookie_samesite'] = &$httpResponse->cookieSameSite;
	}

	/**
	 * Starts and initializes session data.
	 * @throws InvalidStateException
	 */
	public function start(): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			throw new InvalidStateException(
				'Session has been already started.'
				. (ini_get('session.auto_start') === '1' ? ' Probably due to the session.auto_start.' : '')
			);
		}

		$this->configure(self::SECURITY_OPTIONS);
		$this->configure($this->options);

		if (!session_start()) {
			throw new RuntimeException('session_start() failed.');
		}

		$this->started = true;
	}

	/**
	 * Has been session started?
	 */
	public function isStarted(): bool
	{
		return $this->started && session_status() === PHP_SESSION_ACTIVE;
	}

	/**
	 * Ends the current session and store session data.
	 */
	public function close(): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_write_close();
		}
	}

	/**
	 * Destroys all data registered to a session.
	 */
	public function destroy(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			throw new InvalidStateException('Session is not started.');
		}

		session_destroy();
		$_SESSION = null;
		$this->started = false;
		if (!$this->httpResponse->isHeadersSent()) {
			$params = session_get_cookie_params();
			$this->httpResponse->deleteCookie(
				session_name(),
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly'],
				$params['samesite'],
			);
		}
	}

	/**
	 * Regenerates the session ID.
	 * @throws InvalidStateException
	 */
	public function regenerateId(): void
	{
		if ($this->regenerated) {
			return;
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			if (headers_sent($file, $line)) {
				throw new InvalidStateException(
					'Cannot regenerate session ID after HTTP headers have been sent'
					. ($file ? " (output started at $file:$line)." : '.')
				);
			}

			session_regenerate_id(true);
		} else {
			session_id(session_create_id());
		}

		$this->regenerated = true;
	}

	/**
	 * @param mixed[] $config
	 */
	private function configure(array $config): void
	{
		foreach ($config as $key => $value) {

			if ($value === null || ini_get("session.$key") == /* intentionally == */ $value) {
				continue;
			}

			if (session_status() === PHP_SESSION_ACTIVE) {
				throw new InvalidStateException(
					"Unable to set 'session.$key' to value '$value' when session has been started."
				);
			}

			if (ini_set("session.$key", (string) $value) === false) {
				throw new InvalidStateException(
					"Unable to set 'session.$key': ini_set('session.$key', '$value') failed."
				);
			}
		}
	}

}
