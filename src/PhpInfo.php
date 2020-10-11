<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

class PhpInfo
{

	public function getHtml(): string
	{
		$error = 'Cannot get phpinfo() output';
		ob_start();
		phpinfo();
		$info = preg_replace('~^.*?(<table[^>]*>.*</table>).*$~s', '$1', ob_get_clean() ?: $error) ?? $error;
		// Convert inline styles to classes defined in admin/info.css so we can drop CSP style-src 'unsafe-inline'
		$info = str_replace('style="color: #', 'class="color-', $info);
		return sprintf('<div id="phpinfo">%s</div>', $info);
	}

}
