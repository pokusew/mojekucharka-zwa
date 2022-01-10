<?php

declare(strict_types=1);

namespace Core\Utils;

/**
 * A helper for calculating limit and offset from pagination info.
 */
class Paginator
{

	protected int $itemsPerPage = 1;
	protected int $itemsCount = 0;
	protected int $pagesCount = 0;
	protected int $pageNumber = 1;

	protected function recalculatePagesCount(): void
	{
		$this->pagesCount = intdiv($this->itemsCount, $this->itemsPerPage);
		if ($this->itemsCount % $this->itemsPerPage > 0) {
			$this->pagesCount++;
		}
	}

	public function getPagesCount(): int
	{
		return $this->pagesCount;
	}

	public function getItemsPerPage(): int
	{
		return $this->itemsPerPage;
	}

	public function setItemsPerPage(int $itemsPerPage): Paginator
	{
		if ($itemsPerPage <= 0) {
			throw new \InvalidArgumentException(
				"Invalid itemsPerPage '$itemsPerPage'. Items per page must be greater than 0."
			);
		}
		$this->itemsPerPage = $itemsPerPage;
		$this->recalculatePagesCount();
		return $this;
	}

	public function getItemsCount(): int
	{
		return $this->itemsCount;
	}

	public function setItemsCount(int $itemsCount): Paginator
	{
		if ($itemsCount < 0) {
			throw new \InvalidArgumentException(
				"Invalid itemsCount '$itemsCount'. Items count must be greater than or equal to 0."
			);
		}
		$this->itemsCount = $itemsCount;
		$this->recalculatePagesCount();
		return $this;
	}

	public function getPageNumber(): int
	{
		return $this->pageNumber;
	}

	public function setPageNumber(int $pageNumber): Paginator
	{
		if ($pageNumber <= 0) {
			throw new \InvalidArgumentException(
				"Invalid pageNumber '$pageNumber'. Page number must be greater than 0."
			);
		}
		$this->pageNumber = $pageNumber;
		return $this;
	}

	public function getLimit(): int
	{
		return $this->itemsPerPage;
	}

	public function getOffset(): int
	{
		return ($this->pageNumber - 1) * $this->itemsPerPage;
	}

	public function hasPrevPage(): bool
	{
		return $this->pageNumber > 1;
	}

	public function getPrevPageNumber(): ?int
	{
		if ($this->hasPrevPage()) {
			return $this->pageNumber - 1;
		}
		return null;
	}

	public function hasNextPage(): bool
	{
		return $this->pageNumber < $this->pagesCount;
	}

	public function getNextPageNumber(): ?int
	{
		if ($this->hasNextPage()) {
			return $this->pageNumber + 1;
		}
		return null;
	}

	public function getNumberOfFirstItemOnPage(): int
	{
		return $this->getOffset() + 1;
	}

	public function getNumberOfLastItemOnPage(): int
	{
		return min($this->itemsCount, $this->pageNumber * $this->itemsPerPage);
	}

	public function getFirstPageNumber(): int
	{
		return 1;
	}

	public function getLastPageNumber(): int
	{
		return $this->getPagesCount();
	}

	public function isValidPageNumber(int $pageNumber): bool
	{
		return $this->getFirstPageNumber() <= $pageNumber && $pageNumber <= $this->getLastPageNumber();
	}

}
