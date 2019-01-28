<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

use Combyna\Harness\TestCombynaBootstrap;

// Load Composer's autoloader
$autoloader = require __DIR__ . '/../../vendor/autoload.php';

$autoloader->addPsr4('Combyna\\Harness\\', __DIR__ . '/Combyna/Harness');
$autoloader->addPsr4('Combyna\\Integrated\\', __DIR__ . '/Combyna/Integrated');
$autoloader->addPsr4('Combyna\\Integration\\', __DIR__ . '/Combyna/Integration');
$autoloader->addPsr4('Combyna\\Unit\\', __DIR__ . '/Combyna/Unit');

$compiledContainer = __DIR__ . '/../../php/dist/Combyna/Container/CompiledCombynaContainer.php';

if (file_exists($compiledContainer)) {
    // Make sure the compiled DI container is up-to-date for each test run
    unlink($compiledContainer);
}

// Make sure the expression parser is up-to-date for each test run
shell_exec('composer run-script build:expression-parser');

$combynaBootstrap = new TestCombynaBootstrap();
