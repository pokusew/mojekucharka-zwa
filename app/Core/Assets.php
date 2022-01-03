<?php

declare(strict_types=1);

namespace Core;

use InvalidArgumentException;
use JsonException;
use RuntimeException;

/**
 * A simple assets index.
 *
 * Designed to be used with tools like webpack with webpack-assets-manifest.
 *
 * See {@see Assets::loadAssets()} for the format of the assets manifest file.
 *
 * @phpstan-type Asset array{src: string, integrity: string}
 */
class Assets
{

	private Config $config;

	/**
	 * @var ?string assets mode override the {@see Config::$mode} specifically for assets
	 */
	public ?string $assetsMode;
	/**
	 * @var ?string optional webpack-dev-server URL
	 */
	public ?string $webpackDevServerUrl;

	/** @var array<string, Asset> assets index */
	private array $assets;

	/**
	 * @param Config $config app config
	 * @param ?string $assetsMode assets mode, fallbacks to {@see Config::$mode} if not given
	 * @param ?string $assetsManifest absolute filename of the assets manifest file to load,
	 *                                if not given, no manifest is loaded
	 * @param ?string $webpackDevServerUrl optional webpack-dev-server URL
	 */
	public function __construct(
		Config $config,
		?string $assetsMode = null,
		?string $assetsManifest = null,
		?string $webpackDevServerUrl = null
	)
	{
		$this->config = $config;
		$this->assetsMode = $assetsMode;
		$this->webpackDevServerUrl = $webpackDevServerUrl;
		$this->loadAssets($assetsManifest);
	}

	/**
	 * Replaces the assets index with assets index loaded from the given JSON assets manifest file.
	 *
	 * Here is an example of a valid JSON assets manifest file:
	 * ```json
	 * {
	 *   "index.js": {
	 *     "src": "index.ea7d84d94f89f5fd1107.imt.js",
	 *     "integrity": "sha256-eoosgx6Dp9fl+2tKJZiE7SYbgT3/MWljkbq1PG7GWeI="
	 *   },
	 *   "index.css": {
	 *     "src": "style.dbced1889ea7d0228d70.imt.css",
	 *     "integrity": "sha256-s/FBRZkYiAd63hPGEHemoIRr9IrW8apKfovzLSb6bB0="
	 *   },
	 * }
	 * ```
	 *
	 * @param ?string $assetsManifest absolute filename of the assets manifest file to load,
	 *                                `null` results in an empty assets index (i.e. with no asset names)
	 */
	public function loadAssets(?string $assetsManifest): void
	{
		if ($assetsManifest === null) {
			$this->assets = [];
			return;
		}

		$assetsStr = file_get_contents($assetsManifest);
		if ($assetsStr === false) {
			throw new RuntimeException("Could not get file contents of '$assetsManifest'.");
		}
		try {
			$this->assets = json_decode($assetsStr, true, 512, JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			throw new RuntimeException(
				"Could not parse '$assetsManifest': " . $e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

	/**
	 * Checks that the asset with given name exists. If {@see Assets::isModeDevelopment()} an exception is thrown
	 * when when the does not exist (so it can be easily spotted during development).
	 * @param string $assetName
	 * @return bool `true` iff the asset with given name exists the current assets index, `false` otherwise
	 * @throws InvalidArgumentException when the asset with given name does not exist
	 */
	private function ensureValidAssetName(string $assetName): bool
	{
		if (!isset($this->assets[$assetName])) {

			if ($this->isModeDevelopment()) {
				throw new InvalidArgumentException("Invalid asset name '$assetName'.");
			}

			return false;
		}

		return true;
	}

	/**
	 * Gets the URL of the asset with given name
	 * @param bool $fullUrl if `true`, a full URL (http(s)://...) is returned
	 */
	public function getUrl(string $assetName, bool $fullUrl = false): string
	{
		if (!$this->ensureValidAssetName($assetName)) {
			return '#invalid-asset-name';
		}

		$base = $fullUrl ? $this->config->getBaseUrl() : $this->config->basePath;

		if ($this->isModeDevelopment() && !$this->assets[$assetName]['src']) {
			throw new RuntimeException("Missing asset '$assetName' src.");
		}

		return $base . ($this->assets[$assetName]['src'] ?? '#missing-asset-src');
	}

	/**
	 * Gets the integrity (for use as the value of the HTML integrity attribute) of the asset with given name
	 */
	public function getIntegrity(string $assetName): string
	{
		if (!$this->ensureValidAssetName($assetName)) {
			return '#invalid-asset-name';
		}

		if ($this->isModeDevelopment() && !$this->assets[$assetName]['integrity']) {
			throw new RuntimeException("Missing asset '$assetName' integrity.");
		}

		return ($this->assets[$assetName]['integrity'] ?? '#missing-asset-integrity');
	}

	public function isModeDevelopment(): bool
	{
		return ($this->assetsMode ?? $this->config->mode) === Config::DEVELOPMENT;
	}

}
