<?php

declare(strict_types=1);

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

	public string $databaseDsn = 'mysql:host=localhost;dbname=DB;user=USER;password=PASSWORD';

	/**
	 * @param mixed $value
	 * @param string $defaultMode
	 * @return string
	 */
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

	public function isAssetsModeDevelopment(): bool
	{
		return $this->assetsMode === self::DEVELOPMENT;
	}

}
