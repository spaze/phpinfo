<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class PhpInfoTest extends TestCase
{

	public function testGetHtml(): void
	{
		$phpInfo = new PhpInfo();
		$html = $phpInfo->getHtml();
		Assert::contains('<div id="phpinfo">', $html);
		Assert::contains('disable_functions', $html);
	}

}

(new PhpInfoTest())->run();
