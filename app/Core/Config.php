<?php

declare(strict_types=1);

namespace Core;

/**
 * Config provides means of configuration for apps using the Core framework.
 *
 * It encapsulates basic always-needed settings (e.g. mode, https, host, basePath).
 *
 * But is also allows setting dynamic {@see Config::$parameters parameters} that can be used for autowiring
 * by parameter names in the {@see \Core\DI\Container DI Container}.
 *
 * Moreover, it supports declarative definition of factories and services
 * for the {@see \Core\DI\Container DI Container}
 * using {@see Config::$factories} and {@see Config::$services} respectively.
 */
class Config
{

	/**
	 * The recommended {@see Config::$mode mode} value for use in production.
	 */
	public const PRODUCTION = 'production';

	/**
	 * The recommended {@see Config::$mode mode} value for use during development.
	 * In this mode, most exceptions that occur in {@see \Core\App::run()} are left to be handled by the Tracy
	 * (i.e. it shows the blue screen).
	 */
	public const DEVELOPMENT = 'development';

	public const STANDARD_MODES = [
		self::DEVELOPMENT,
		self::PRODUCTION,
	];

	/**
	 * @var string Application mode affects the framework behavior, especially error handling.
	 */
	public string $mode = self::PRODUCTION;

	public ?string $debuggerEmail = null;
	public ?string $debuggerLogDirectory = null;

	/**
	 * @var bool Use `https` instead of `http` when `true`.
	 *           It affects link generation in full URL mode.
	 *           It may be also used for auto redirection to https (not implemented yet, left to the web-server).
	 *          {@see \Core\Forms\Form} may it also use as a part of the CSRF protection (referrer/origin check).
	 */
	public bool $https = false;

	/**
	 * @var string The expected value of the HTTP Host header. It should also contain the port number
	 *             (if an nonstandard port is used). Format: `domain-or-ip[:port]`.
	 *             It affects link generation in full URL mode.
	 *             It may be also used for request verification of the actual HTTP Host header value,
	 *             though it is not implemented yet a such a check is left to the web-server.
	 *             {@see \Core\Forms\Form} may it also use as a part of the CSRF protection (referrer/origin check).
	 */
	public string $host;

	/**
	 * @var string URL path prefix. Must start and also en with the slash `/`.
	 *             Affects routing, link generation, cookies.
	 *             {@see \Core\Forms\Form} may it also use as a part of the CSRF protection (referrer/origin check).
	 */
	public string $basePath = '/';

	/**
	 * @var string namespace of the app's presenter classes
	 */
	public string $presenterNamespace = 'App\Presenter';

	/**
	 * @var array<string, mixed> dynamic parameters that can be used for autowiring
	 *                           by parameter names in the {@see \Core\DI\Container DI Container}
	 */
	public array $parameters = [];

	/**
	 * @var mixed[] DI factories to automatically register within the {@see \Core\DI\Container}
	 * @see \Core\DI\Container::registerFactory()
	 */
	public array $factories = [];

	/**
	 * @var string[] services automatically to add to the {@see \Core\DI\Container}
	 * @phpstan-var class-string[] services automatically to add to the {@see \Core\DI\Container}
	 * @see \Core\DI\Container::addByType()
	 */
	public array $services = [];

	public function __construct()
	{
		$this->host = "${_SERVER['SERVER_NAME']}:${_SERVER['SERVER_PORT']}";
		// default factories
		$this->factories[] = 'Core\Http\HttpRequestFactory::createHttpRequest';
		$this->factories[] = 'Core\Http\HttpResponseFactory::createHttpResponse';
	}

	/**
	 * Returns the given value iff it is a valid standard mode (is in the {@see Config::STANDARD_MODES} array).
	 * @param mixed $value the value to check
	 * @param string $defaultMode fallback value to return if the given value is not a valid standard mode
	 * @return string the `value` iff the `value` is in the {@see Config::STANDARD_MODES}, `defaultMode` otherwise.
	 */
	public static function parseMode($value, string $defaultMode): string
	{
		if (!in_array($value, self::STANDARD_MODES, true)) {
			return $defaultMode;
		}

		return $value;
	}

	public function isModeDevelopment(): bool
	{
		return $this->mode === self::DEVELOPMENT;
	}

	/**
	 * @phpstan-return 'https'|'http'
	 */
	public function getUrlScheme(): string
	{
		return $this->https ? 'https' : 'http';
	}

	/**
	 * Constructs and returns the full base URL (always with the trailing slash).
	 * @return string `{getUrlScheme()}://{$host}{$basePath}`
	 */
	public function getBaseUrl(): string
	{
		return $this->getUrlScheme() . '://' . $this->host . $this->basePath;
	}

}
