<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

$autoloader = require __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('Combyna\\Harness\\', __DIR__ . '/Combyna/Harness');
$autoloader->addPsr4('Combyna\\Integration\\', __DIR__ . '/Combyna/Integration');
$autoloader->addPsr4('Combyna\\Unit\\', __DIR__ . '/Combyna/Unit');
