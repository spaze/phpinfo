<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

class PhpInfo
{

	private bool $sanitizeSessionId = true;

	private string $sanitizeWith = '[***]';

	/** @var array<string, string> */
	private array $sanitize = [];


	public function getHtml(): string
	{
		$error = 'Cannot get phpinfo() output';
		ob_start();
		phpinfo();
		$info = preg_replace('~^.*?(<table[^>]*>.*</table>).*$~s', '$1', ob_get_clean() ?: $error) ?? $error;
		// Convert inline styles to classes defined in admin/info.css so we can drop CSP style-src 'unsafe-inline'
		$replacements['style="color: #'] = 'class="color-';
		$sanitize = [];
		if ($this->sanitizeSessionId && $this->getSessionId() !== null) {
			$sanitize[$this->getSessionId()] = $this->sanitizeWith;
		}
		$sanitize = $this->sanitize + $sanitize;
		foreach ($sanitize as $search => $replace) {
			$search = (string)$search;
			$replacements[$search] = $replace;
			$replacements[urlencode($search)] = $replace;
		}
		$info = strtr($info, $replacements);
		return sprintf('<div id="phpinfo">%s</div>', $info);
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
		$this->sanitize[$sanitize] = $with ?? $this->sanitizeWith;
		return $this;
	}

}
