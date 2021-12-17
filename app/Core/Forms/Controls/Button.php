<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Core\Forms\Form;
use Nette\Utils\Html;

class Button extends BaseControl
{

	protected Html $htmlEl;

	public function __construct(string $name, $label, ?string $type = 'submit')
	{
		parent::__construct($name);
		$this->htmlEl = Html::el('button');
		$this->htmlEl->setText($label);
		$this->htmlEl->type = $type;
		$this->generateId();
	}

	public function getElem(): Html
	{
		return $this->htmlEl;
	}

	protected function generateId()
	{
		$id = ($this->form !== null ? $this->form->getName() . '--' : '') . $this->name;
		$this->htmlEl->id = $id;
	}

	public function setForm(?Form $form): self
	{
		parent::setForm($form);
		$this->generateId();
		return $this;
	}

	public function getType(): ?string
	{
		return $this->htmlEl->type;
	}

	public function setType(?string $type): self
	{
		$this->htmlEl->type = $type;
		return $this;
	}

	public function __toString(): string
	{
		return (string) $this->htmlEl;
	}

}
