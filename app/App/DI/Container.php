<?php

namespace App\DI;

class Container
{

	private array $factoriesForType = [];
	private array $instancesByType = [];

	public function __construct()
	{

	}

	public function add(object $obj)
	{
		$this->instancesByType[get_class($obj)] = $obj;
	}

	public function registerFactoryForType(string $class, callable $factory)
	{
		$this->factoriesForType[$class] = $factory;
	}

	private function determineConstructorArgs(\ReflectionClass $class, \ReflectionMethod $constructor): array
	{

		$args = [];

		$parameters = $constructor->getParameters();

		foreach ($parameters as $index => $param) {

			$paramType = $param->getType();

			if ($paramType === null) {
				throw new \InvalidArgumentException(
					"Cannot autowire constructor param '$param' with no type information."
				);
			}

			$value = $this->getByType($paramType->getName());

			$args[] = $value;

		}

		return $args;

	}

	public function createByType(string $class): object
	{

		if (isset($this->factoriesForType[$class])) {
			return call_user_func($this->factoriesForType[$class]);
		}

		try {

			$rc = new \ReflectionClass($class);

			if (!$rc->isInstantiable()) {
				throw new \InvalidArgumentException("Class '$class' is not instantiable.");
			}

			$constructor = $rc->getConstructor();

			$args = $constructor === null || !$constructor->isPublic()
				? []
				: $this->determineConstructorArgs($rc, $constructor);

			$instance = $rc->newInstanceArgs($args);

			$props = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);

			foreach ($props as $prop) {

				$propType = $prop->getType();

				if ($propType === null) {
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

			return $instance;


		} catch (\ReflectionException $e) {
			throw new \InvalidArgumentException("Class '$class' not found.");
		}

	}

	public function getByType(string $class, $autoCreate = true): ?object
	{

		if ($class === self::class) {
			return $this;
		}

		if (isset($this->instancesByType[$class])) {
			return $this->instancesByType[$class];
		}

		if (!$autoCreate) {
			return null;
		}

		$this->instancesByType[$class] = $this->createByType($class);

		return $this->instancesByType[$class];

	}

}
