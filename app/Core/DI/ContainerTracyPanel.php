<?php

declare(strict_types=1);

namespace Core\DI;

use Tracy\IBarPanel;

/**
 * A Tracy extension for DI Container
 *
 * @see https://tracy.nette.org/en/extensions
 *
 * @phpstan-import-type Stats from \Core\DI\Container
 */
class ContainerTracyPanel implements IBarPanel
{

	private Container $container;

	/** @phpstan-var Stats|null captured stats about the container */
	private ?array $stats = null;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	private function captureStats(): void
	{
		if ($this->stats === null) {
			$this->stats = $this->container->getStats();
		}
	}

	public function getTab(): string
	{
		$this->captureStats();

		ob_start();
		require __DIR__ . '/container-tracy-tab.template.php';
		return ob_get_clean();
	}

	public function getPanel(): string
	{
		$this->captureStats();

		ob_start();
		require __DIR__ . '/container-tracy-panel.template.php';
		return ob_get_clean();
	}

}
