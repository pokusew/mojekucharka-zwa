<?php

declare(strict_types=1);

namespace Core\Forms;

use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\TextInput;
use Core\Http\HttpRequest;
use Nette\Utils\Html;

class Form implements \ArrayAccess
{

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	private string $name;

	private string $method;

	private Html $htmlEl;

	private bool $submitted = false;

	/** @var BaseControl[] */
	private array $controls = [];

	public ?string $error = null;

	public ?bool $valid = null;

	/** @var callable[] */
	public array $onSuccess;

	public function __construct(string $name, string $method = self::METHOD_POST)
	{
		$this->name = $name;
		$this->method = $method;
		$this->htmlEl = Html::el('form');
		$this->htmlEl->name = $name;
		$this->htmlEl->method = strtolower($method);
		$this->htmlEl->action = '';
	}

	public function getElem(): Html {
		return $this->htmlEl;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function addText(string $name, string $label): TextInput
	{
		$control = new TextInput($name, $label);
		$control->setForm($this);

		$this->controls[$name] = $control;

		return $control;
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

	public function validate(): bool
	{
		return false;
	}

	public function process(HttpRequest $httpRequest): bool
	{

		if ($this->submitted) {
			return false;
		}

		if ($httpRequest->method !== $this->method) {
			return false;
		}

		$rawValues = $httpRequest->post;

		foreach ($this->controls as $name => $control) {

			if (isset($rawValues[$name])) {
				$control->setValue($rawValues[$name]);
			}

		}

		return true;

	}

	public function isSubmitted(): bool
	{
		return $this->submitted;
	}

}
