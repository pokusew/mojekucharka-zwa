<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Core\Forms\Form;
use Nette\Utils\Html;

/**
 * Base class for all form controls that are represented by a HTML element.
 */
abstract class HtmlControl extends BaseControl
{

	protected Html $htmlEl;

	public function __construct(string $name, string $htmlElementName)
	{
		parent::__construct($name);
		$this->htmlEl = Html::el($htmlElementName);
		// We use the `n_` prefix to avoid interference with JavaScript access to the form's properties and elements.
		// See https://developer.mozilla.org/en-US/docs/web/api/htmlformelement#issues_with_naming_elements
		$this->htmlEl->name = 'n_' . $name;
		$this->generateId();
	}

	/**
	 * @return $this
	 */
	public function setRequired(bool $required = true, ?string $msg = null): self
	{
		parent::setRequired($required);
		$this->htmlEl->required = $required;
		$this->htmlEl->attrs['data-required-msg'] = $msg;
		return $this;
	}

	public function getElem(): Html
	{
		return $this->htmlEl;
	}

	protected function generateId(): void
	{
		$id = ($this->form !== null ? $this->form->getName() . '--' : '') . $this->name;
		$this->htmlEl->id = $id;
	}

	/**
	 * @return $this
	 */
	public function setForm(?Form $form): self
	{
		parent::setForm($form);
		$this->generateId();
		return $this;
	}

	public function __toString(): string
	{
		return (string) $this->htmlEl;
	}

}
