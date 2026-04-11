<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use function ob_get_clean;
use function ob_start;
use function phpinfo;
use function preg_replace;
use function sprintf;
use function str_replace;
use const INFO_ALL;

final class PhpInfo
{

	private SensitiveValueSanitizer $sanitizer;


	public function __construct(?SensitiveValueSanitizer $sanitizer = null)
	{
		$this->sanitizer = $sanitizer ?? new SensitiveValueSanitizer();
	}


	public function getHtml(int $flags = INFO_ALL): string
	{
		$error = '<div id="phpinfo">Cannot get phpinfo() output</div>';
		ob_start();
		phpinfo($flags);
		$info = ob_get_clean();
		if ($info === false || $info === '') {
			return $error;
		}
		$info = preg_replace('~^.*?(<table[^>]*>.*</table>).*$~s', '$1', $info);
		if ($info === null) {
			return $error;
		}
		// Convert inline styles to classes defined in src/assets/info.css so we can drop CSP style-src 'unsafe-inline'
		$info = str_replace('style="color: #', 'class="color-', $info);
		$info = $this->sanitizer->sanitize($info);
		return sprintf('<div id="phpinfo">%s</div>', $info);
	}


	public function getFullPageHtml(int $flags = INFO_ALL): string
	{
		ob_start();
		phpinfo($flags);
		$info = ob_get_clean();
		if ($info === false || $info === '') {
			return 'Cannot get phpinfo() output';
		}
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
