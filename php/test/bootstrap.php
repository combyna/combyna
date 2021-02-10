<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

use Combyna\Test\Harness\TestCombynaBootstrap;
use Symfony\Component\Filesystem\Filesystem;

// Load Composer's autoloader
$autoloader = require __DIR__ . '/../../vendor/autoload.php';

$autoloader->addPsr4('Combyna\\Harness\\', __DIR__ . '/Combyna/Harness');
$autoloader->addPsr4('Combyna\\Integrated\\', __DIR__ . '/Combyna/Integrated');
$autoloader->addPsr4('Combyna\\Integration\\', __DIR__ . '/Combyna/Integration');
$autoloader->addPsr4('Combyna\\Unit\\', __DIR__ . '/Combyna/Unit');

$rootPath = __DIR__ . '/../..';
$relativeCachePath = 'php/dist';

// Make sure the cache is up-to-date for each test run
$fileSystem = new Filesystem();
$fileSystem->remove($rootPath . '/' . $relativeCachePath);

$combynaBootstrap = new TestCombynaBootstrap([], $rootPath, $relativeCachePath);

$combynaBootstrap->warmUp();
