<?php

declare(strict_types=1);

namespace Core\Routing;

/**
 * Represents bi-directional mapping between an URL path and a logical address.
 *
 * Logical address is a combination of presenter name and optionally action name together with key-value array
 * of parameters. For example: `Article:view, ['id' => 8]`
 */
abstract class Route
{

	/**
	 * The separator in the logical address between the presenter and the action
	 */
	public const LOGICAL_SEPARATOR = ':';

	/**
	 * @var string name of the presenter, must correspond to the class with name:
	 *             `{Config::$presenterNamespace}\{$name}Presenter`
	 */
	public string $presenter;

	/**
	 * @var ?string optional name of the presenter action
	 */
	public ?string $action;

	/**
	 * Tries to match the given URL path against this route.
	 * @param string $path URL path
	 * @return array<string, mixed>|null the parameters with their values on match, `null` otherwise
	 */
	public abstract function match(string $path): ?array;

	/**
	 * Constructs the URL for this route.
	 *
	 * In most cases, you want to use rather the {@see Router::link()} that takes into account the config
	 * and can return the full URL.
	 *
	 * @param array<string, mixed> $params the parameters with their values for the link
	 * @param bool $throwOnMissingParams if `true` and a required parameter is missing,
	 *                                   an {@see \InvalidArgumentException} is thrown
	 */
	public abstract function link(array $params = [], bool $throwOnMissingParams = false): string;

	public function getLogicalAddress(): string
	{
		return $this->presenter . self::LOGICAL_SEPARATOR . ($this->action ?? '');
	}

}
