<?php

namespace Core;

class Assets
{

	private Config $config;
	private array $assets;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->loadAssets();
	}

	private function loadAssets()
	{

		if (!isset($this->config->assetsManifest)) {
			$this->assets = [];
			return;
		}

		$assetsStr = file_get_contents($this->config->assetsManifest);
		$this->assets = json_decode($assetsStr, true);

	}

	private function ensureValidAssetName(string $assetName): bool
	{
		if (!isset($this->assets[$assetName])) {
			if ($this->config->isDevelopment()) {
				throw new \InvalidArgumentException("Invalid asset name '$assetName'.");
			}
			return false;
		}
		return true;
	}

	public function getUrl(string $assetName): string
	{

		if (!$this->ensureValidAssetName($assetName)) {
			return '#invalid-asset-name';
		}

		return $this->config->basePath . ($this->assets[$assetName]['src'] ?? '#missing-asset-src');

	}

	public function getIntegrity(string $assetName): string
	{

		if (!$this->ensureValidAssetName($assetName)) {
			return '#invalid-asset-name';
		}

		return ($this->assets[$assetName]['integrity'] ?? '#missing-asset-src');

	}

}
