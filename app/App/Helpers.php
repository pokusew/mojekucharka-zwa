<?php

declare(strict_types=1);

namespace App;

use Core\Forms\Controls\HtmlWithLabelControl;
use Core\Forms\Controls\Select;
use Core\Forms\Form;
use Core\Utils\Paginator;
use DateTime;
use DateTimeInterface;
use Nette\Utils\Html;

/**
 * App-specific helpers, mainly form rendering.
 */
class Helpers
{

	/**
	 * Renders the given form control to HTML
	 *
	 * Note! It also mutates the HTML elements attributes of the given control.
	 *
	 * @param HtmlWithLabelControl $control
	 * @return string HTML
	 */
	public static function renderFormControl(HtmlWithLabelControl $control): string
	{
		$group = Html::el('div');
		// @phpstan-ignore-next-line
		$group->class[] = 'form-group';

		$label = $control->getLabel()->class('form-control-label');

		$input = $control->getElem()->class('form-control');

		$group->insert(null, $label);
		$group->insert(null, $input);

		if ($control->hasError()) {

			$control->getElem()->data('touched', 'true');

			$feedback = Html::el('p');
			$feedback->class('form-control-feedback');
			$feedback->setText($control->getError());

			$group->class[] = 'has-error';
			$group->insert(null, $feedback);

		}

		return (string) $group;
	}

	/**
	 * Converts options to option elements and inserts them into the given parent element.
	 * @param Html $parent parent node where the option elements will be inserted
	 * @param array<string|int, mixed> $options the options
	 * @param string|int|null $selected the selected value
	 */
	protected static function addSelectOptionsToParent(Html $parent, array $options, $selected): void
	{
		foreach ($options as $value => $label) {
			$option = Html::el('option');
			$option->value = (string) $value;
			$option->setText($label);
			if ($selected !== null && $selected == $value /* intentional == */) {
				$option->selected = true;
			}
			$parent->insert(null, $option);
		}
	}

	/**
	 * Renders the given form select control to HTML
	 *
	 * Note! It also mutates the HTML elements attributes of the given control.
	 *
	 * @param Select $control
	 * @return string HTML
	 */
	public static function renderSelect(Select $control): string
	{
		$group = Html::el('div');
		// @phpstan-ignore-next-line
		$group->class[] = 'form-group';

		$label = $control->getLabel()->class('form-control-label');

		$select = $control->getElem()->class('form-control');

		$group->insert(null, $label);
		$group->insert(null, $select);

		if ($control->getGroupedOptions() !== null) {
			foreach ($control->getGroupedOptions() as $label => $options) {
				$optgroup = Html::el('optgroup');
				$optgroup->label = $label;
				self::addSelectOptionsToParent($optgroup, $options, $control->getValue());
				$select->insert(null, $optgroup);
			}
		} else {
			self::addSelectOptionsToParent($select, $control->getOptions(), $control->getValue());
		}

		if ($control->hasError()) {

			$control->getElem()->data('touched', 'true');

			$feedback = Html::el('p');
			$feedback->class('form-control-feedback');
			$feedback->setText($control->getError());

			$group->class[] = 'has-error';
			$group->insert(null, $feedback);

		}

		return (string) $group;
	}

	/**
	 * Renders the global error (if any)) of the given form
	 *
	 * Note! It also mutates the HTML elements attributes of the given control.
	 *
	 * @return string HTML
	 */
	public static function renderFormError(Form $form): string
	{
		if (!$form->hasGlobalError()) {
			return '';
		}

		$feedback = Html::el('p');
		$feedback->class('form-control-feedback form-error');
		$feedback->setText($form->getGlobalError());

		return (string) $feedback;
	}

	/**
	 * Renders pagination.
	 * @param Paginator $paginator
	 * @param callable $createLink
	 * @phpstan-param (callable(int $pageNumber): string) $createLink
	 * @param string|null $ariaLabel pagination aria-label
	 * @return string HTML
	 */
	public static function renderPagination(
		Paginator $paginator,
		callable $createLink,
		?string $ariaLabel = null
	): string
	{
		$nav = Html::el('nav');
		// @phpstan-ignore-next-line
		$nav->class[] = 'pagination';

		if ($ariaLabel !== null) {
			$nav->setAttribute('aria-label', $ariaLabel);
		}

		$list = Html::el('ul');

		$nav->insert(null, $list);

		$prevPageNumber = $paginator->getPrevPageNumber();
		$prevPageItem = self::createPaginationItem(
			true,
			$prevPageNumber !== null ? $createLink($prevPageNumber) : null,
			false,
			$prevPageNumber === null,
		);
		$prevPageItem->getChildren()[0]->setAttribute('aria-label', 'Předchozí');
		$prevPageItem->getChildren()[0]->setHtml('<span aria-hidden="true">&laquo;</span>');

		$list->insert(null, $prevPageItem);

		$firstPageNumber = $paginator->getFirstPageNumber();
		$lastPageNumber = $paginator->getLastPageNumber();
		for ($pageNumber = $firstPageNumber; $pageNumber < $lastPageNumber; $pageNumber++) {
			$pageItem = self::createPaginationItem(
				(string) $pageNumber,
				$createLink($pageNumber),
				$paginator->getPageNumber() === $pageNumber,
			);
			$list->insert(null, $pageItem);
		}

		$nextPageNumber = $paginator->getNextPageNumber();
		$nextPageItem = self::createPaginationItem(
			null,
			$nextPageNumber !== null ? $createLink($nextPageNumber) : null,
			false,
			$nextPageNumber === null,
		);
		$nextPageItem->getChildren()[0]->setAttribute('aria-label', 'Následující');
		$nextPageItem->getChildren()[0]->setHtml('<span aria-hidden="true">&raquo;</span>');

		$list->insert(null, $nextPageItem);

		return (string) $nav;
	}

	/**
	 * Renders pagination item.
	 * @param mixed $text
	 * @param ?string $href
	 * @param bool $active
	 * @param bool $disabled
	 * @return Html the li node
	 */
	protected static function createPaginationItem(
		$text = null,
		?string $href = null,
		bool $active = false,
		bool $disabled = false
	): Html
	{
		$item = Html::el('li');

		if ($disabled) {
			// @phpstan-ignore-next-line
			$item->class[] = 'disabled';
			if ($text !== null) {
				$span = Html::el('span');
				$span->setText($text);
				$item->insert(null, $span);
			}
		} else {
			$link = Html::el('a');
			$link->href = $href;
			$link->setText($text);
			$item->insert(null, $link);
		}

		if ($active) {
			// @phpstan-ignore-next-line
			$item->class[] = 'active';
			$item->setAttribute('aria-current', 'page');
		}

		return $item;
	}

	public static function stringToHtml(string $str): string
	{
		return str_replace(["\r\n", "\n", "\r"], '<br/>', htmlspecialchars($str));
	}

	/**
	 * Creates a new {@see DateTime} from the given datetime string received from MySQL database via PDO.
	 * @param string $dateStr a datetime string received from MySQL database via PDO
	 * @return DateTime
	 */
	public static function ds(string $dateStr): DateTime
	{
		return DateTime::createFromFormat('Y-m-d H:i:s', $dateStr);
	}

	/**
	 * Creates and returns HTML time element.
	 * @param DateTime $dateTime
	 * @param string $userFormat
	 * @return Html an HTML time element
	 */
	public static function timeEl(DateTime $dateTime, string $userFormat = 'j. n. Y \v H:i'): Html
	{
		$time = Html::el('time');
		$time->datetime = $dateTime->format(DateTimeInterface::W3C);
		$dateTime->setTimezone(new \DateTimeZone( 'Europe/Prague'));
		$time->setText($dateTime->format($userFormat));
		return $time;
	}

}
