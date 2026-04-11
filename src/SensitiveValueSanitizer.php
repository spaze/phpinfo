<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use function is_string;
use function session_id;
use function session_name;
use function strtr;
use function urlencode;

final class SensitiveValueSanitizer
{

	private bool $sanitizeSessionId = true;

	/** @var array<string, string> */
	private array $sanitize = [];


	public function __construct(private string $sanitizeWith = '[***]')
	{
	}


	public function sanitize(string $info): string
	{
		$sanitize = [];
		if ($this->sanitizeSessionId) {
			$sessionId = $this->getSessionId();
			if ($sessionId !== null) {
				$sanitize[$sessionId] = $this->sanitizeWith;
				$sanitize[urlencode($sessionId)] = $this->sanitizeWith;
			}
		}
		return strtr($info, $this->sanitize + $sanitize);
	}


	private function getSessionId(): ?string
	{
		$sessionId = session_id();
		if ($sessionId !== false && $sessionId !== '') {
			return $sessionId;
		}
		$sessionName = session_name();
		if ($sessionName === false) {
			$sessionId = null;
		} else {
			$sessionId = $_COOKIE[$sessionName] ?? null;
		}
		return is_string($sessionId) && $sessionId !== '' ? $sessionId : null;
	}


	/**
	 * WARNING: Not recommended, disabling session id sanitization may allow
	 * session stealing attacks that read the cookie from the output of phpinfo().
	 */
	public function doNotSanitizeSessionId(): self
	{
		$this->sanitizeSessionId = false;
		return $this;
	}


	public function addSanitization(string $sanitize, ?string $with = null): self
	{
		if ($sanitize === '') {
			return $this;
		}
		$this->sanitize[$sanitize] = $this->sanitize[urlencode($sanitize)] = $with ?? $this->sanitizeWith;
		return $this;
	}

}
