<?php

declare(strict_types=1);

namespace Core\DI;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;

class Container
{

	/**
	 * @var array<string, callable>
	 */
	private array $factories = [];

	/**
	 * Instances grouped by their types (FQ class/interface names)
	 * @var array<string, array<object>>
	 */
	private array $instances = [];

	public function __construct()
	{
		$this->add($this);
	}

	/**
	 * Adds an existing instance to the container so it can be used for the autowiring
	 * @return $this
	 */
	public function add(object $instance): self
	{
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
	 * @param string|null $type if null, the return type of factory method is used
	 * @return void
	 */
	public function registerFactory(string $classMethod, string $type = null)
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

			$this->factories[$type] = [$instance, $method->getName()];

		} catch (ReflectionException $e) {
			throw new InvalidArgumentException(
				"Could not register factory method '$classMethod' because it does not exist.",
				0,
				$e,
			);
		}

		// $this->factories[$type] = $factory;

	}

	/**
	 * @template T of object
	 * @param ReflectionFunctionAbstract $function
	 * @param ReflectionClass<T>|null $class
	 * @return mixed[]
	 */
	private function autowireFunctionParameters(
		ReflectionFunctionAbstract $function,
		ReflectionClass $class = null
	): array
	{

		$args = [];

		$parameters = $function->getParameters();

		foreach ($parameters as $index => $param) {

			$paramType = $param->getType();

			if (!($paramType instanceof ReflectionNamedType)) {
				$functionName = ($class !== null ? $class->getName() . '::' : '') . $function->getName();
				throw new InvalidArgumentException(
					"Cannot autowire '$functionName' param '$param' with no type information."
				);
			}

			if ($paramType->isBuiltin()) {
				$functionName = ($class !== null ? $class->getName() . '::' : '') . $function->getName();
				throw new InvalidArgumentException(
					"Cannot autowire '$functionName' param '$param' with builtin type '$paramType'."
				);
			}

			$value = $this->getByType($paramType->getName());

			$args[] = $value;

		}

		return $args;

	}

	/**
	 * @template T of object
	 * @param T $instance
	 * @param ReflectionClass<T>|null $class
	 * @throws ReflectionException
	 */
	private function autowireInstanceProperties(object $instance, ReflectionClass $class = null): void
	{

		if ($class === null) {
			$class = new ReflectionClass($instance);
		}

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

			$instance->$propName = $this->getByType($propType->getName());

		}

	}

	public function createByType(string $type): object
	{

		if (isset($this->factories[$type])) {
			return call_user_func($this->factories[$type]);
		}

		try {

			$class = new ReflectionClass($type);

			if (!$class->isInstantiable()) {
				throw new InvalidArgumentException("Class '$type' is not instantiable.");
			}

			$constructor = $class->getConstructor();

			$args = $constructor === null || !$constructor->isPublic()
				? []
				: $this->autowireFunctionParameters($constructor, $class);

			$instance = $class->newInstanceArgs($args);

			$this->autowireInstanceProperties($instance, $class);

			return $instance;


		} catch (ReflectionException $e) {
			throw new InvalidArgumentException("Class '$type' not found.");
		}

	}

	public function getByType(string $type, bool $autoCreate = true): ?object
	{

		if (isset($this->instances[$type]) && count($this->instances[$type]) === 1) {
			return $this->instances[$type][0];
		}

		if (!$autoCreate) {
			return null;
		}

		$this->instances[$type][] = $this->createByType($type);

		return $this->instances[$type][0];

	}

}
