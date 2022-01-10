<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Exceptions\BadRequestException;
use Core\Http\HttpResponse;
use Tracy\Debugger;
use Tracy\ILogger;

// TODO: improve before final submission
class ErrorPresenter extends BasePresenter
{

	public function action(): void
	{
		if ($this->exception instanceof BadRequestException) {
			$this->view = $this->exception->getCode() === HttpResponse::S_404_NOT_FOUND ? 'error-404' : 'error-500';
			$this->httpResponse->setCode(404);
		} else {
			Debugger::log($this->exception, ILogger::ERROR);
			$this->view = 'error-500';
			$this->httpResponse->setCode(500);
		}
	}

}
