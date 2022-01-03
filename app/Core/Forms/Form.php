<?php

declare(strict_types=1);

namespace Core\Forms;

use ArrayAccess;
use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\Button;
use Core\Forms\Controls\TextInput;
use Core\Http\HttpRequest;
use Nette\Utils\Html;

/**
 * @phpstan-implements ArrayAccess<string, BaseControl>
 * @phpstan-import-type HtmlLabel from \Core\Forms\Controls\HtmlWithLabelControl
 */
class Form implements ArrayAccess
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
		// enable custom validation using JS (see frontend/scripts/forms.ts)
		$this->htmlEl->data('validation', true);
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

	/**
	 * @param string $name
	 * @phpstan-param HtmlLabel $label
	 * @param mixed $label
	 * @return TextInput
	 */
	public function addText(string $name, $label): TextInput
	{
		$control = new TextInput($name, $label);

		$this->addControl($control);

		return $control;
	}

	/**
	 * @param string $name
	 * @phpstan-param HtmlLabel $label
	 * @param mixed $label
	 * @return Button
	 */
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

		// TODO: implement CSRF protection using Referer and Origin headers check

		$this->submitted = true;

		$data = $httpRequest->method === self::METHOD_GET ? $httpRequest->query : $httpRequest->post;

		foreach ($this->controls as $name => $control) {
			$control->setValueFromRequest($data);
		}

		if ($this->validate()) {
			foreach ($this->onSuccess as $handler) {
				$handler($this);
			}
		}

		return true;
	}

	public function isSubmitted(): bool
	{
		return $this->submitted;
	}

}
