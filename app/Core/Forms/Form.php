<?php

declare(strict_types=1);

namespace Core\Forms;

use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\Button;
use Core\Forms\Controls\TextInput;
use Core\Http\HttpRequest;
use Nette\Utils\Html;

class Form implements \ArrayAccess
{

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	protected string $name;

	protected string $method;

	protected Html $htmlEl;

	protected bool $submitted = false;

	/** @var BaseControl[] */
	protected array $controls = [];

	protected ?string $error = null;

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

	public function getElem(): Html
	{
		return $this->htmlEl;
	}

	public function getName(): string
	{
		return $this->name;
	}

	protected function addControl(BaseControl $control): BaseControl
	{
		$control->setForm($this);
		$this->controls[$control->getName()] = $control;
		return $control;
	}

	public function addText(string $name, string $label): TextInput
	{
		$control = new TextInput($name, $label);

		$this->addControl($control);

		return $control;
	}

	public function addSubmit(string $name, $label): Button
	{
		$control = new Button($name, $label, 'submit');

		$this->addControl($control);

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
		$valid = true;
		foreach ($this->controls as $name => $control) {
			if (!$control->validate()) {
				$valid = false;
				// do not break, we want to trigger validation of all controls
			}
		}
		return $valid;
	}

	public function process(HttpRequest $httpRequest): bool
	{

		if ($this->submitted) {
			return false;
		}

		if ($httpRequest->method !== $this->method) {
			return false;
		}

		$this->submitted = true;

		$rawValues = $httpRequest->post;

		foreach ($this->controls as $name => $control) {

			if (isset($rawValues[$name]) && is_string($rawValues[$name])) {
				$control->setValue($rawValues[$name]);
			}

		}

		if ($this->validate()) {
			foreach ($this->onSuccess as $handler) {
				call_user_func($handler);
			}
		}

		return true;

	}

	public function isSubmitted(): bool
	{
		return $this->submitted;
	}

}
