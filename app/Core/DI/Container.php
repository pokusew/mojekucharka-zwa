<?php

declare(strict_types=1);

namespace Core\DI;

use Core\Config;
use Core\Exceptions\AutowireException;
use Core\Exceptions\InstanceCreationException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

/**
 * A simple dependency injection (DI) container with autowiring support.
 *
 * TODO: detect and prevent circular dependencies
 *
 * @phpstan-type Stats array{factories: array<class-string, string>, numUniqueInstances: int, instances: array<class-string, int>}
 */
class Container
{

	private Config $config;

	/**
	 * @var array<string, callable>
	 */
	private array $factories = [];

	private int $numUniqueInstances = 0;

	/**
	 * Instances grouped by their types (FQ class/interface names)
	 * @template T of object
	 * @var array<class-string<T>, array<T>>
	 */
	private array $instances = [];

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->add($this);
		$this->add($config);
		$this->applyConfig();
	}

	/**
	 * Registers factories from {@see \Core\Config::$factories} and add services from {@see \Core\Config::$services}.
	 * @return void
	 */
	private function applyConfig(): void
	{
		foreach ($this->config->factories as $factoryDef) {

			if (is_string($factoryDef)) {
				$this->registerFactory($factoryDef);
				continue;
			}

			// $factoryDef = [$classMethod, $type]
			if (
				is_array($factoryDef)
				&& count($factoryDef) === 2
				&& isset($factoryDef[0])
				&& isset($factoryDef[1])
				&& is_string($factoryDef[0])
				&& is_string($factoryDef[1])
			) {
				$this->registerFactory($factoryDef[0], $factoryDef[1]);
				continue;
			}

			throw new InvalidArgumentException("Invalid factory definition '$factoryDef'.");

		}

		foreach ($this->config->services as $serviceType) {
			$this->addByType($serviceType);
		}
	}

	/**
	 * Adds an existing instance to the container so it can be used for the autowiring
	 * @return $this
	 */
	public function add(object $instance): self
	{
		$this->numUniqueInstances++;
		$types = class_parents($instance) + class_implements($instance) + [get_class($instance)];
		foreach ($types as $type) {
			$this->instances[$type][] = $instance;
		}
		return $this;
	}

	/**
	 * Registers a method that will be used for creating instances of the given type.
	 * @param string $classMethod Class name and method name delimited by ::
	 *                            (e.g. Core\HttpRequestFactory::createHttpRequest)
	 * @param string|null $type if `null`, the return type of factory method is used
	 * @return $this
	 */
	public function registerFactory(string $classMethod, string $type = null): self
	{
		try {

			$method = new ReflectionMethod($classMethod);
			$returnType = $method->getReturnType();
			$class = $method->getDeclaringClass();

			if ($type === null) {

				if (!($returnType instanceof ReflectionNamedType)) {
					throw new InvalidArgumentException(
						"Factory method '$classMethod' has no or an invalid return type"
						. " and no explicit type was given.",
					);
				}

				$type = $returnType->getName();

				if ($returnType->isBuiltin()) {
					throw new InvalidArgumentException(
						"Factory method '$classMethod' has invalid return type '$type'.",
					);
				}

			}

			$instance = $this->getByType($class->getName());

			if (isset($this->factories[$type])) {
				throw new RuntimeException(
					"Could not register factory method '$classMethod' for type '$type'"
					. " because another factory is already registered."
				);
			}

			$this->factories[$type] = [$instance, $method->getName()];

			return $this;

		} catch (ReflectionException $e) {
			throw new RuntimeException(
				"Could not register factory method '$classMethod' because it does not exist.",
				0,
				$e,
			);
		}
	}

	/**
	 * Autowires the given parameter.
	 *
	 * The autowired value for the parameter is determined using the following algorithm:
	 * 1. If the parameter is a method's parameter and {@see Config::$parameters} has any of the following keys,
	 *    use the corresponding value.
	 *      1. `{getDeclaringClass()->getName()}.{the parameter's name}`
	 *      2. `{getDeclaringClass()->getShortName()}.{the parameter's name}`
	 *      3. `{lcfirst(getDeclaringClass()->getShortName())}.{the parameter's name}`
	 * 2. It the parameter's type hint is class name and {@see Container::getByType()} returns an instance, use it.
	 * 3. If the parameter has a default value, use it.
	 *
	 * @param ReflectionParameter $param
	 * @return mixed the autowired value for the given parameter
	 * @throws AutowireException when the value could not be determined using the autowiring algorithm
	 */
	private function autowireFunctionParameter(ReflectionParameter $param)
	{
		$class = $param->getDeclaringClass();

		// 1. If the parameter is a method's parameter and {@see Config::$parameters} has any of the following keys,
		// 	  use the corresponding value.
		if ($class !== null) {
			$configParameterKeys = [
				// order matters
				$class->getName() . '.' . $param->getName(),
				$class->getShortName() . '.' . $param->getName(),
				lcfirst($class->getShortName()) . '.' . $param->getName(),
			];
			foreach ($configParameterKeys as $configParameterKey) {
				if (isset($this->config->parameters[$configParameterKey])) {
					return $this->config->parameters[$configParameterKey];
				}
			}
		}

		// 2. It the parameter's type hint is class name and {@see Container::getByType()} returns an instance, use it.
		$instanceCreationException = null;
		$paramType = $param->getType();
		if ($paramType instanceof ReflectionNamedType && !$paramType->isBuiltin()) {
			try {
				// @phpstan-ignore-next-line
				return $this->getByType($paramType->getName());
			} catch (InstanceCreationException $e) {
				// ignore, continue with the autowiring algorithm
				$instanceCreationException = $e;
			}
		}

		// 3. If the parameter has a default value, use it.
		if ($param->isDefaultValueAvailable()) {
			return $param->getDefaultValue();
		}

		$functionName = ($class !== null ? $class->getName() . '::' : '') . $param->getDeclaringFunction()->getName();
		throw new AutowireException(
			"Cannot autowire '$functionName' param '$param'.",
			0,
			$instanceCreationException,
		);
	}

	/**
	 * Autowires parameters of the given function.
	 * @param ReflectionFunctionAbstract $function
	 * @return mixed[] ordered number-indexed array of the autowired function parameters
	 * @throws AutowireException
	 * @see Container::autowireFunctionParameter()
	 */
	private function autowireFunctionParameters(ReflectionFunctionAbstract $function): array
	{
		$args = [];
		foreach ($function->getParameters() as $param) {
			$args[] = $this->autowireFunctionParameter($param);
		}
		return $args;
	}

	/**
	 * Autowires properties of the instance.
	 * @template T of object
	 * @param T $instance
	 * @param ReflectionClass<T> $class
	 * @throws AutowireException
	 */
	private function autowireInstanceProperties(object $instance, ReflectionClass $class): void
	{
		$props = $class->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($props as $prop) {

			$propType = $prop->getType();

			if (!($propType instanceof ReflectionNamedType)) {
				continue;
			}

			$propDocComment = $prop->getDocComment();

			if ($propDocComment === false) {
				continue;
			}

			if (!preg_match('/@inject/', $propDocComment)) {
				continue;
			}

			$propName = $prop->name;

			try {
				// @phpstan-ignore-next-line
				$instance->$propName = $this->getByType($propType->getName());
			} catch (InstanceCreationException $e) {
				throw new AutowireException(
					"Cannot autowire property '$propName' of $class->name instance: " . $e->getMessage(),
					0,
					$e,
				);
			}

		}
	}

	/**
	 * Creates a new instance of the given type but does NOT store it in the registry.
	 * @template T of object
	 * @phpstan-param class-string<T> $type
	 * @return T
	 * @throws InstanceCreationException when the instance could not be created
	 */
	public function createByType(string $type): object
	{
		if (isset($this->factories[$type])) {
			return call_user_func($this->factories[$type]);
		}

		try {

			$class = new ReflectionClass($type);

			if (!$class->isInstantiable()) {
				throw new InstanceCreationException("Class '$type' is not instantiable.");
			}

			try {

				$constructor = $class->getConstructor();

				$args = $constructor !== null ? $this->autowireFunctionParameters($constructor) : [];

				$instance = $class->newInstanceArgs($args);

				$this->autowireInstanceProperties($instance, $class);

				return $instance;

			} catch (AutowireException $e) {
				throw new InstanceCreationException(
					"A new instance of '$type' could not be created due an autowiring exception: "
					. $e->getMessage(),
					0,
					$e,
				);
			}

		} catch (ReflectionException $e) {
			throw new InstanceCreationException("Class '$type' not found.", 0, $e);
		}
	}

	/**
	 * Gets the instance of the given type from the registry.
	 * @template T of object
	 * @phpstan-param class-string<T> $type
	 * @param bool $autoCreate whether to create a new instance if none exists
	 *                         and store the reference to it in the registry
	 * @return T|null the instance, or `null` when
	 *                (a) no instance of the given type exists and $autoCreate is `false`
	 *                (b) or more than one instance of the given type exists (so it is ambiguous which one to return)
	 * @throws InstanceCreationException
	 */
	public function getByType(string $type, bool $autoCreate = true): ?object
	{
		if (isset($this->instances[$type])) {
			if (count($this->instances[$type]) === 1) {
				return $this->instances[$type][0];
			} else {
				// more than one instance of the given type exists
				return null;
			}
		}

		// no instance of the given type exists $autoCreate is `false`
		if (!$autoCreate) {
			return null;
		}

		// create a new instance because none exists and store the reference to it in the registry
		$instance = $this->createByType($type);
		$this->add($instance);

		return $instance;
	}

	/**
	 * Adds a new instance of the given type to the registry.
	 * @phpstan-param class-string $type
	 * @throws InstanceCreationException
	 */
	public function addByType(string $type): void
	{
		$this->add($this->createByType($type));
	}

	/**
	 * Checks if there is at least one instance of the given type in the registry.
	 * @phpstan-param class-string $type
	 */
	public function hasInstance(string $type): bool
	{
		return isset($this->instances[$type]) && count($this->instances[$type]) > 1;
	}

	/**
	 * Get number of instances of the given type in the registry.
	 * @phpstan-param class-string $type
	 */
	public function getNumInstances(string $type): int
	{
		return isset($this->instances[$type]) ? count($this->instances[$type]) : 0;
	}

	/**
	 * Get stats about the current state of this container.
	 * @return Stats
	 */
	public function getStats(): array
	{
		/** @var Stats $stats */
		$stats = [
			'factories' => [],
			'numUniqueInstances' => $this->numUniqueInstances,
			'instances' => [],
		];
		foreach ($this->factories as $type => $factory) {
			$stats['factories'][$type] = get_class($factory[0]) . '::' . $factory[1];
		}
		foreach ($this->instances as $type => $instances) {
			$stats['instances'][$type] = count($instances);
		}
		return $stats;
	}

}
