<?php
declare(strict_types = 1);

use Composer\Autoload\ClassLoader;
use Tester\Environment;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Spaze\\PhpInfo\\', __DIR__);

Environment::setup();
