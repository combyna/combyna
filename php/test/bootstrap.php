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
$autoloader->add('Combyna\\Integration\\', __DIR__ . '/Integration');
$autoloader->add('Combyna\\Unit\\', __DIR__ . '/Unit');
