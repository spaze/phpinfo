<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use Tester\Assert;
use Tester\TestCase;
use function session_destroy;
use function session_set_save_handler;
use function session_start;
use function urlencode;

require __DIR__ . '/bootstrap.php';

class PhpInfoTest extends TestCase
{

	private const SESSION_ID = 'foobar,baz';
	private const WALDO_1337 = 'waldo-fred-1337';
	private const WALDO_1338 = 'waldo-quux-1338';


	protected function setUp(): void
	{
		$_SERVER['HTTP_WALDO_FRED'] = self::WALDO_1337;
		$_SERVER['HTTP_COOKIE'] = 'PHPSESSID=' . urlencode(self::SESSION_ID);
		$_COOKIE['PHPSESSID'] = self::SESSION_ID;

		session_set_save_handler(new TestSessionHandler(self::SESSION_ID));
		session_start();
	}


	protected function tearDown(): void
	{
		session_destroy();
	}


	public function testGetHtml(): void
	{
		$html = (new PhpInfo())->getHtml();
		Assert::contains('<div id="phpinfo">', $html);
		Assert::contains('disable_functions', $html);
		Assert::contains('class="color-', $html);
		Assert::notContains('style="color: #', $html);
	}


	public function testGetFullPageHtml(): void
	{
		$html = (new PhpInfo())->getFullPageHtml();
		Assert::notContains('<div id="phpinfo">', $html);
		Assert::contains('disable_functions', $html);
		Assert::notContains('class="color-', $html);
		Assert::contains('style="color: #', $html);
	}


	public function testGetHtmlSessionIdSanitization(): void
	{
		$html = (new PhpInfo())->getHtml();
		Assert::notContains(self::SESSION_ID, $html);
		Assert::notContains(urlencode(self::SESSION_ID), $html);
		Assert::contains('[***]', $html);
	}


	public function testGetHtmlSessionIdSanitizationCustomReplacement(): void
	{
		$phpInfo = new PhpInfo();
		$phpInfo->addSanitization(self::SESSION_ID, 'yeah, sure');
		Assert::contains('yeah, sure', $phpInfo->getHtml());
	}


	public function testGetHtmlDoNotSanitizeSessionIdButWhy(): void
	{
		$phpInfo = new PhpInfo();
		$html = $phpInfo->doNotSanitizeSessionId()->getHtml();
		Assert::contains(self::SESSION_ID, $html);
		Assert::contains(urlencode(self::SESSION_ID), $html);
	}


	public function testGetHtmlAddSanitization(): void
	{
		$phpInfo = new PhpInfo();
		Assert::contains(self::WALDO_1337, $phpInfo->getHtml());
		$html = $phpInfo->addSanitization(self::WALDO_1337, self::WALDO_1338)->getHtml();
		Assert::notContains(self::WALDO_1337, $html);
		Assert::contains(self::WALDO_1338, $html);
	}


	public function testGetHtmlWithFlagsIncludesSection(): void
	{
		$html = (new PhpInfo())->getHtml(INFO_CONFIGURATION);
		Assert::contains('<div id="phpinfo">', $html);
		Assert::contains('disable_functions', $html);
	}


	public function testGetHtmlWithFlagsExcludesSection(): void
	{
		$html = (new PhpInfo())->getHtml(INFO_GENERAL);
		Assert::contains('<div id="phpinfo">', $html);
		Assert::notContains('disable_functions', $html);
	}


	public function testGetFullPageHtmlWithFlagsIncludesSection(): void
	{
		$html = (new PhpInfo())->getFullPageHtml(INFO_CONFIGURATION);
		Assert::notContains('<div id="phpinfo">', $html);
		Assert::contains('disable_functions', $html);
	}


	public function testGetFullPageHtmlWithFlagsExcludesSection(): void
	{
		$html = (new PhpInfo())->getFullPageHtml(INFO_GENERAL);
		Assert::notContains('<div id="phpinfo">', $html);
		Assert::notContains('disable_functions', $html);
	}


	public function testGetHtmlSessionIdSanitizationCustomSanitizer(): void
	{
		$sanitizer = new SensitiveValueSanitizer();
		$sanitizer->addSanitization(self::SESSION_ID, '🍕');
		$html = (new PhpInfo($sanitizer))->getHtml();
		Assert::notContains(self::SESSION_ID, $html);
		Assert::notContains(urlencode(self::SESSION_ID), $html);
		Assert::contains('🍕', $html);
	}

}

(new PhpInfoTest())->run();
