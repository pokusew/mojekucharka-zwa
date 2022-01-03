<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Exceptions\InvalidStateException;

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

	/**
	 * SameSite cookie attribute values
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
	 */
	public const
		SAME_SITE_LAX = 'Lax',
		SAME_SITE_STRICT = 'Strict',
		SAME_SITE_NONE = 'None';

	private int $code = self::S_200_OK;

	public ?string $cookieDomain = '';
	public ?string $cookiePath = '/';
	public bool $cookieSecure = true;
	public bool $cookieHttpOnly = true;
	public ?string $cookieSameSite = self::SAME_SITE_LAX;

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

	/**
	 * Sends a cookie.
	 * @return $this
	 */
	public function setCookie(
		string $name,
		string $value,
		int $expires = 0,
		?string $path = null,
		?string $domain = null,
		?bool $secure = null,
		?bool $httpOnly = null,
		?string $sameSite = null
	): self
	{
		$options = [
			'expires' => $expires,
			'path' => $path ?? $this->cookiePath,
			'domain' => $domain ?? $this->cookieDomain,
			'secure' => $secure ?? $this->cookieSecure,
			'httponly' => $httpOnly ?? $this->cookieHttpOnly,
			'samesite' => $sameSite ?? $this->cookieSameSite,
		];
		// @phpstan-ignore-next-line
		setcookie($name, $value, $options);
		return $this;
	}

	/**
	 * Deletes a cookie.
	 * @return $this
	 */
	public function deleteCookie(
		string $name,
		?string $path = null,
		?string $domain = null,
		?bool $secure = null,
		?bool $httpOnly = null,
		?string $sameSite = null
	): self
	{
		// from https://www.php.net/manual/en/function.setcookie.php:
		//   Cookies must be deleted with the same parameters as they were set with.
		//   If the value argument is an empty string,
		//   and all other arguments match a previous call to setcookie,
		//   then the cookie with the specified name will be deleted from the remote client.
		//   This is internally achieved by setting value to 'deleted' and expiration time in the past.
		$this->setCookie($name, '', 1, $path, $domain, $secure, $httpOnly, $sameSite);
		return $this;
	}

}
