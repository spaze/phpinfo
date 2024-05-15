<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class SensitiveValueSanitizerNoSessionTest extends TestCase
{

	private const SESSION_ID = 'foobar,baz';


	public function testSanitizeSessionNotStarted(): void
	{
		$_COOKIE[session_name()] = self::SESSION_ID;
		$string = (new SensitiveValueSanitizer())->sanitize(sprintf('waldo %s fred', self::SESSION_ID));
		Assert::contains('waldo', $string);
		Assert::notContains(self::SESSION_ID, $string);
		Assert::notContains(urlencode(self::SESSION_ID), $string);
		Assert::contains('[***]', $string);
	}

}

(new SensitiveValueSanitizerNoSessionTest())->run();
