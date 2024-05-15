<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

class PhpInfo
{

	private SensitiveValueSanitizer $sanitizer;


	public function __construct(?SensitiveValueSanitizer $sanitizer = null)
	{
		$this->sanitizer = $sanitizer ?? new SensitiveValueSanitizer();
	}


	public function getHtml(): string
	{
		$error = 'Cannot get phpinfo() output';
		ob_start();
		phpinfo();
		$info = preg_replace('~^.*?(<table[^>]*>.*</table>).*$~s', '$1', ob_get_clean() ?: $error) ?? $error;
		// Convert inline styles to classes defined in admin/info.css so we can drop CSP style-src 'unsafe-inline'
		$info = str_replace('style="color: #', 'class="color-', $info);
		$info = $this->sanitizer->sanitize($info);
		return sprintf('<div id="phpinfo">%s</div>', $info);
	}


	public function getFullPageHtml(): string
	{
		$error = 'Cannot get phpinfo() output';
		ob_start();
		phpinfo();
		$info = ob_get_clean() ?: $error;
		return $this->sanitizer->sanitize($info);
	}


	/**
	 * WARNING: Not recommended, disabling session id sanitization may allow
	 * session stealing attacks that read the cookie from the output of phpinfo().
	 */
	public function doNotSanitizeSessionId(): self
	{
		$this->sanitizer->doNotSanitizeSessionId();
		return $this;
	}


	public function addSanitization(string $sanitize, ?string $with = null): self
	{
		$this->sanitizer->addSanitization($sanitize, $with);
		return $this;
	}

}
