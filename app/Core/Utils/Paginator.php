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
	/**
	 * @var int There is always at least one page (though it may be completely empty).
	 */
	protected int $pagesCount = 1;
	/**
	 * @var int The first page has $pageNumber === 1, there is **no page** with number 0.
	 */
	protected int $pageNumber = 1;

	protected function recalculatePagesCount(): void
	{
		$fullPagesCount = intdiv($this->itemsCount, $this->itemsPerPage);
		// if there is a non-full last page
		if ($this->itemsCount % $this->itemsPerPage > 0) {
			$this->pagesCount = $fullPagesCount + 1;
		}
		else {
			// There is always at least one page (though it may be completely empty).
			$this->pagesCount = max(1, $fullPagesCount);
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
		if (!($itemsPerPage >= 1)) {
			throw new \InvalidArgumentException(
				"Invalid itemsPerPage = $itemsPerPage. It must hold: itemsPerPage >= 1."
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
		if (!($itemsCount >= 0)) {
			throw new \InvalidArgumentException(
				"Invalid itemsCount = $itemsCount. It must hold: itemsCount >= 0."
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
		if (!($pageNumber >= 1)) {
			throw new \InvalidArgumentException(
				"Invalid pageNumber = $pageNumber. It must hold: pageNumber >= 0."
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
		return $this->pageNumber > $this->getFirstPageNumber();
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
		return $this->pageNumber < $this->getLastPageNumber();
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
		return min($this->itemsCount, $this->getOffset() + 1);
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
