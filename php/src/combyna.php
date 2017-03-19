<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

use Combyna\CombynaBootstrap;

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

$combynaBootstrap = new CombynaBootstrap();

return $combynaBootstrap->getContainer()->get('combyna');
