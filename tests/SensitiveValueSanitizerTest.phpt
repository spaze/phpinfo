<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class SensitiveValueSanitizerTest extends TestCase
{

	private const SESSION_ID = 'foobar,baz';

	private string $string;


	public function __construct()
	{
		$this->string = sprintf('waldo %s fred %s quux', self::SESSION_ID, urlencode(self::SESSION_ID));
	}


	protected function setUp(): void
	{
		session_set_save_handler(new TestSessionHandler(self::SESSION_ID));
		session_start();
	}


	protected function tearDown(): void
	{
		session_destroy();
	}


	public function testSanitize(): void
	{
		$string = (new SensitiveValueSanitizer())->sanitize($this->string);
		Assert::contains('waldo', $string);
		Assert::notContains(self::SESSION_ID, $string);
		Assert::notContains(urlencode(self::SESSION_ID), $string);
		Assert::contains('[***]', $string);
	}


	public function testSanitizeSessionIdCustomReplacement(): void
	{
		$sanitizer = new SensitiveValueSanitizer();
		$sanitizer->addSanitization(self::SESSION_ID, 'yeah, sure');
		$string = $sanitizer->sanitize($this->string);
		Assert::contains('waldo', $string);
		Assert::notContains(self::SESSION_ID, $string);
		Assert::notContains(urlencode(self::SESSION_ID), $string);
		Assert::contains('yeah, sure', $string);
	}


	public function testSanitizeDoNotSanitizeSessionIdButWhy(): void
	{
		$string = (new SensitiveValueSanitizer())->doNotSanitizeSessionId()->sanitize($this->string);
		Assert::contains('waldo', $string);
		Assert::contains(self::SESSION_ID, $string);
		Assert::contains(urlencode(self::SESSION_ID), $string);
		Assert::notContains('[***]', $string);
	}


	public function testSanitizeAddSanitization(): void
	{
		$string = (new SensitiveValueSanitizer())->addSanitization('setec', '🍌')->sanitize('setec astronomy');
		Assert::notContains('setec', $string);
		Assert::contains('🍌', $string);
	}


	public function testGetHtmlNumericSessionId(): void
	{
		$sessionId = '31337';

		// Set a new session id
		session_destroy();
		session_set_save_handler(new TestSessionHandler($sessionId));
		session_start();

		Assert::noError(function () use ($sessionId, &$html): void {
			$html = (new SensitiveValueSanitizer())->sanitize("31336 + 1 = {$sessionId}");
		});
		Assert::notContains($sessionId, $html);
	}

}

(new SensitiveValueSanitizerTest())->run();
