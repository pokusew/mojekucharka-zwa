<?php

namespace Core;

class Config
{

	public const PRODUCTION = 'production';
	public const DEVELOPMENT = 'development';
	public const DEFAULT_MODES = [
		self::DEVELOPMENT,
		self::PRODUCTION,
	];

	public string $mode = self::PRODUCTION;

	public ?string $debuggerEmail = null;
	public ?string $debuggerLogDirectory = null;

	public bool $https = false;
	public string $host;
	public string $basePath = '/';

	public string $assetsMode = self::DEVELOPMENT;
	public ?string $assetsManifest = null;
	public ?string $webpackDevServer = null;

	public string $presenterNamespace = 'App\Presenter';

	public static function parseMode($value, string $defaultMode): string
	{

		if (!in_array($value, self::DEFAULT_MODES, true)) {
			return $defaultMode;
		}

		return $value;

	}

	public function isDevelopment(): bool
	{
		return $this->mode === self::DEVELOPMENT;
	}

}
