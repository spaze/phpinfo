<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

class SensitiveValueSanitizer
{

	private bool $sanitizeSessionId = true;

	private string $sanitizeWith = '[***]';

	/** @var array<string, string> */
	private array $sanitize = [];


	public function sanitize(string $info): string
	{
		$sanitize = [];
		if ($this->sanitizeSessionId && $this->getSessionId() !== null) {
			$sanitize[$this->getSessionId()] = $this->sanitizeWith;
			$sanitize[urlencode($this->getSessionId())] = $this->sanitizeWith;
		}
		return strtr($info, $this->sanitize + $sanitize);
	}


	private function getSessionId(): ?string
	{
		return session_id() ?: null;
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
		$this->sanitize[$sanitize] = $this->sanitize[urlencode($sanitize)] = $with ?? $this->sanitizeWith;
		return $this;
	}

}
