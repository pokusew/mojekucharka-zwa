<?php

declare(strict_types=1);

namespace Core\Forms;

use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\TextInput;

class Form implements \ArrayAccess
{

	private string $method = 'POST';

	/**
	 * @var BaseControl[]
	 */
	private array $controls = [];

	public ?string $error;

	public ?bool $valid;

	/**
	 * @var callable[]
	 */
	public array $onSuccess;

	public function __construct()
	{
	}

	public function addText(string $name)
	{
		$this->controls[$name] = new TextInput($name);
	}

	public function offsetExists($offset): bool
	{
		return isset($this->controls[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->controls[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->controls[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->controls[$offset]);
	}

	public function validate(): bool {
		return false;
	}

	public function isSubmitted(\Core\Http\HttpRequest $httpRequest): bool
	{
		return $httpRequest->method === $this->method;
	}

	public function process(\Core\Http\HttpRequest $httpRequest)
	{
		$rawValues = $httpRequest->post;

		foreach ($this->controls as $name => $control) {

			if (isset($rawValues[$name])) {
				$control->setValue($rawValues[$name]);
			}

		}



	}



}
