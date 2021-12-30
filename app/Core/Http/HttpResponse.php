<?php

declare(strict_types=1);

namespace Core\Http;

use Core\InvalidStateException;

class HttpResponse
{

	/**
	 * HTTP response status codes
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
	 */
	public const
		S_200_OK = 200,
		S_201_CREATED = 201,
		S_204_NO_CONTENT = 204,
		S_300_MULTIPLE_CHOICES = 300,
		S_301_MOVED_PERMANENTLY = 301,
		S_302_FOUND = 302,
		S_303_SEE_OTHER = 303,
		S_304_NOT_MODIFIED = 304,
		S_307_TEMPORARY_REDIRECT = 307,
		S_308_PERMANENT_REDIRECT = 308,
		S_400_BAD_REQUEST = 400,
		S_401_UNAUTHORIZED = 401,
		S_403_FORBIDDEN = 403,
		S_404_NOT_FOUND = 404,
		S_405_METHOD_NOT_ALLOWED = 405,
		S_410_GONE = 410,
		S_500_INTERNAL_SERVER_ERROR = 500,
		S_501_NOT_IMPLEMENTED = 501,
		S_503_SERVICE_UNAVAILABLE = 503;

	private int $code = self::S_200_OK;

	/**
	 * @return $this
	 */
	public function setCode(int $code): self
	{
		$this->code = $code;
		http_response_code($code);
		return $this;
	}

	public function getCode(): int
	{
		return $this->code;
	}

	/**
	 * Checks if HTTP headers have been sent
	 */
	public function isHeadersSent(): bool
	{
		return headers_sent();
	}

	/**
	 * Returns the value of the HTTP header or the default value
	 */
	public function getHeader(string $header, ?string $default = null): ?string
	{
		// TODO: consider using https://www.php.net/manual/en/function.apache-response-headers.php
		$header .= ':';
		$len = strlen($header);
		foreach (headers_list() as $item) {
			if (strncasecmp($item, $header, $len) === 0) {
				return ltrim(substr($item, $len));
			}
		}
		return $default;
	}

	/**
	 * Returns a list of response headers sent (or ready to send)
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		$headers = [];
		foreach (headers_list() as $header) {
			$a = strpos($header, ':');
			$headers[substr($header, 0, $a)] = (string) substr($header, $a + 2);
		}
		return $headers;
	}

	/**
	 * @return $this
	 */
	public function setHeader(string $name, ?string $value, bool $replace = true): self
	{
		if (headers_sent($file, $line)) {
			throw new InvalidStateException(
				"Cannot set header after HTTP headers have been sent (output started at $file:$line)."
			);
		}

		if ($value === null) {
			header_remove($name);
		} else {
			header($name . ': ' . $value, $replace, $this->code);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function addHeader(string $name, string $value): self
	{
		return $this->setHeader($name, $value);
	}

	/**
	 * @return $this
	 */
	public function removeHeader(string $name): self
	{
		return $this->setHeader($name, null);
	}

	/**
	 * @return $this
	 */
	public function setContentType(string $type, ?string $charset = null): self
	{
		return $this->setHeader('Content-Type', $type . ($charset !== null ? '; charset=' . $charset : ''));
	}

	/**
	 * Redirects to a new URL. Note: call `exit()` after it
	 */
	public function redirect(string $url, int $code = self::S_302_FOUND): void
	{
		$this->setCode($code);
		$this->setHeader('Location', $url);

		$safeUrl = htmlspecialchars($url);

		echo <<<END
			<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1.0" />
					<title>Redirect</title>
				</head>
				<body>
					<a href="$safeUrl">Please click here to continue</a>.
				</body>
			</html>
		END;
	}

	// TODO: implement simple cookies abstraction

	// /**
	//  * Sends a cookie.
	//  * @param string name of the cookie
	//  * @param string value
	//  * @param mixed expiration as unix timestamp or number of seconds; Value 0 means "until the browser is closed"
	//  * @param string
	//  * @param string
	//  * @param bool
	//  * @param bool
	//  * @return void
	//  */
	// public function setCookie($name, $value, $expire, $path = NULL, $domain = NULL, $secure = NULL, $httpOnly = NULL) {
	//
	// }
	//
	// /**
	//  * Deletes a cookie.
	//  * @param string name of the cookie.
	//  * @param string
	//  * @param string
	//  * @param bool
	//  * @return void
	//  */
	// public function deleteCookie($name, $path = NULL, $domain = NULL, $secure = NULL) {
	//
	// }

}
