<?php

declare(strict_types=1);

namespace Core\Routing;

use InvalidArgumentException;

/**
 * A route that uses regex for matching and support parameters. Parameters should be named groups in the regex.
 * @phpstan-type ParamTransformer callable(string $value): mixed
 * @phpstan-type LinkGenerator callable(array<string, mixed> $params): string
 */
class RegexRoute extends Route
{

	/**
	 * @var string the URL path pattern to match, must be a valid regex, parameters should be named groups
	 */
	public string $pattern;

	/**
	 * @phpstan-var array<string, ParamTransformer> optional parameters transformers
	 * @var array<string, callable> optional parameters transformers
	 */
	public $paramTransformers;

	/**
	 * @phpstan-var LinkGenerator function that handles URL construction from parameters' values
	 * @var callable function that handles URL construction from parameters' values
	 */
	public $toLink;

	/**
	 * @param string $pattern
	 * @phpstan-param LinkGenerator $toLink function that handles URL construction from parameters' values
	 * @param callable $toLink function that handles URL construction from parameters' values
	 * @param string $presenter
	 * @param ?string $action
	 * @phpstan-param array<string, ParamTransformer> $paramTransformers optional parameters transformers
	 * @param array<string, callable> $paramTransformers optional parameters transformers
	 */
	public function __construct(
		string $pattern,
		callable $toLink,
		string $presenter,
		?string $action = null,
		array $paramTransformers = []
	)
	{
		$this->pattern = $pattern;
		$this->toLink = $toLink;
		$this->presenter = $presenter;
		$this->action = $action;
		$this->paramTransformers = $paramTransformers;
	}

	public function match(string $path): ?array
	{
		if (preg_match($this->pattern, $path, $matches) === 1) {
			$params = [];
			foreach ($matches as $name => $value) {
				// named group
				if (is_string($name)) {
					if (isset($this->paramTransformers[$name])) {
						$params[$name] = call_user_func($this->paramTransformers[$name], $value);
					} else {
						$params[$name] = $value;
					}
				}
			}
			return $params;
		}

		// no match
		return null;
	}

	public function link(array $params = [], bool $throwOnMissingParams = false): string
	{
		return call_user_func($this->toLink, function ($paramName) use (&$params, &$throwOnMissingParams) {

			if ($throwOnMissingParams && !isset($params[$paramName])) {
				$routeName = $this->getLogicalAddress();
				throw new InvalidArgumentException(
					"Missing value for the parameter '$paramName' of the route '$routeName'."
				);
			}

			return $params[$paramName] ?? "#missing-param-$paramName";

		});
	}

}
