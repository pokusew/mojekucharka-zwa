<?php

declare(strict_types=1);

namespace Core\Forms;

use ArrayAccess;
use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\Button;
use Core\Forms\Controls\CheckBox;
use Core\Forms\Controls\Select;
use Core\Forms\Controls\TextArea;
use Core\Forms\Controls\TextInput;
use Core\Http\HttpRequest;
use InvalidArgumentException;
use Nette\Utils\Html;

/**
 * An abstraction of HTML form that simplifies creation and processing.
 *
 * It allows declarative building of the form.
 *
 * Once the form is submitted using {@see Form::process()} it cannot be further modified.
 *
 * @phpstan-implements ArrayAccess<string, BaseControl>
 * @phpstan-import-type HtmlLabel from \Core\Forms\Controls\HtmlWithLabelControl
 */
class Form implements ArrayAccess
{

	/**
	 * Allows values for the form method
	 */
	public const
		METHOD_GET = 'GET',
		METHOD_POST = 'POST';

	protected string $name;

	protected string $method;

	protected Html $htmlEl;

	protected bool $submitted = false;

	/** @var BaseControl[] */
	protected array $controls = [];

	protected ?string $globalError = null;

	/**
	 * @phpstan-var (callable(Form $form): void)[] handlers that will be called on submission if the form is valid
	 */
	public array $onSuccess = [];

	/**
	 * Creates a new form.
	 * @param string $name form name (immutable, cannot be changed once the instance is created)
	 * @param string $method form method (immutable, cannot be changed once the instance is created)
	 */
	public function __construct(string $name, string $method = self::METHOD_POST)
	{
		$this->name = $name;
		$this->method = $method;
		$this->htmlEl = Html::el('form');
		$this->htmlEl->name = $name;
		$this->htmlEl->method = strtolower($method);

		// enable custom validation using JS (see frontend/scripts/forms.ts)
		$this->htmlEl->data('validation', true);
	}

	/**
	 * Sets the form action attribute.
	 *
	 * **NOTE 1:** The action attribute does not have to specified. Per the current HTML5 spec,
	 * when there is no action set, the URL of the form document will be used instead.
	 * See Step 12 in [HTML5 4.10.21.3 Form submission algorithm](https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#form-submission-algorithm).
	 *
	 * **NOTE 2:** But if the action attribute is specified,
	 * it must have must have a value that is a valid non-empty URL.
	 * See [HTML5 4.10.18.6 Form submission attributes](https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#form-submission-attributes).
	 *
	 * @param string|null $action if `null` the action attribute is removed
	 * @return $this
	 */
	public function setAction(?string $action): self
	{
		$this->htmlEl->action = $action;
		return $this;
	}

	/**
	 * Returns the HTML form element of this form.
	 *
	 * It use this for example for adding class or other HTML attributes.
	 *
	 * **NOTE:** If you change name, method or action you may break the form.
	 *
	 * @return Html the HTML form element of this form
	 */
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
	 * Adds a new {@see TextInput}
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
	 * Adds a new {@see TextArea}
	 * @param string $name
	 * @phpstan-param HtmlLabel $label
	 * @param mixed $label
	 * @return TextArea
	 */
	public function addTextArea(string $name, $label): TextArea
	{
		$control = new TextArea($name, $label);

		$this->addControl($control);

		return $control;
	}

	/**
	 * Adds a new {@see Select}
	 * @param string $name
	 * @phpstan-param HtmlLabel $label
	 * @param mixed $label
	 * @return Select
	 */
	public function addSelect(string $name, $label): Select
	{
		$control = new Select($name, $label);

		$this->addControl($control);

		return $control;
	}

	/**
	 * Adds a new {@see CheckBox}
	 * @param string $name
	 * @phpstan-param HtmlLabel $label
	 * @param mixed $label
	 * @return CheckBox
	 */
	public function addCheckBox(string $name, $label): CheckBox
	{
		$control = new CheckBox($name, $label);

		$this->addControl($control);

		return $control;
	}

	/**
	 * Adds a new {@see Button} with type set to `submit`
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

	/**
	 * Checks if the given offset is valid (i.e. if it can be used form control name)
	 * @param mixed $offset
	 * @throws InvalidArgumentException if the offset is not valid
	 */
	protected function ensureValidOffset($offset): void
	{
		if (!is_string($offset)) {
			$offsetType = gettype($offset);
			throw new InvalidArgumentException("Invalid Form offset '$offset' of type $offsetType.");
		}
	}

	public function offsetExists($offset): bool
	{
		// no need to validate offset here
		return isset($this->controls[$offset]);
	}

	public function offsetGet($offset)
	{
		// no need to validate offset here
		return $this->controls[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->ensureValidOffset($offset);

		if (!($value instanceof BaseControl)) {
			$baseControlClass = BaseControl::class;
			throw new InvalidArgumentException(
				"Invalid value attempted to set for offset '$offset'."
				. " Value must be an instance of $baseControlClass."
			);
		}

		if ($value->getName() !== $offset) {
			$expectedOffsetName = $value->getName();
			throw new InvalidArgumentException(
				"Invalid offsetSet: given offset '$offset' !== the control name '$expectedOffsetName'."
			);
		}

		$this->addControl($value);
	}

	public function offsetUnset($offset)
	{
		// no need to validate offset here
		unset($this->controls[$offset]);
	}

	/**
	 * Validates all controls of this form.
	 * @return bool `true` if all controls are valid and at the same time the form has no global error
	 *               (i.e. {@see Form::hasGlobalError() is `false`}
	 */
	public function validate(): bool
	{
		$valid = true;
		foreach ($this->controls as $name => $control) {
			if (!$control->validate()) {
				$valid = false;
				// do not break, we want to trigger validation of all controls
			}
		}
		return $valid && !$this->hasGlobalError();
	}

	/**
	 * Returns the form global error if set
	 * @return string|null
	 */
	public function getGlobalError(): ?string
	{
		return $this->globalError;
	}

	/**
	 * Sets the form global error
	 * @return $this
	 */
	public function setGlobalError(?string $globalError): self
	{
		$this->globalError = $globalError;
		return $this;
	}

	/**
	 * Clears the form global error
	 * @return $this
	 */
	public function clearGlobalError(): self
	{
		$this->globalError = null;
		return $this;
	}

	/**
	 * Checks if the form has the global error set.
	 *
	 * **NOTE:** This may return `false` even if the form is NOT valid.
	 *
	 * @return bool `true` if the form global error was set
	 */
	public function hasGlobalError(): bool
	{
		return $this->globalError !== null;
	}

	/**
	 * Tries to submit the form using the given HTTP request.
	 *
	 * If this form was already submitted, it immediately returns `false` and does nothing.
	 *
	 * Currently, it only checks if the HTTP request's method matches the form's method.
	 * If so, it considers it as submission. In the future we may implement support for another checks.
	 *
	 * If the form is submitted, values of all its controls are populated from the HTTP request data.
	 * Then the form is validated. If the validation succeeds, all {@see Form::$onSuccess} handlers will be invoked
	 * in the order they were defined in and the form instance will be passed as their first argument.
	 *
	 * @return bool `true` if the form was submitted during this call, `false` otherwise
	 */
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

	/**
	 * @return bool `true` if the form was submitted using {@see Form::process()}
	 */
	public function isSubmitted(): bool
	{
		return $this->submitted;
	}

}
