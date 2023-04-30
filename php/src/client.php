<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

function is_int($value) {
    return true;
}
function is_string($value) {
    return true;
}

use Combyna\CombynaBootstrap;

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

$combynaBootstrap = new CombynaBootstrap();

$container = $combynaBootstrap->createContainer();

return $container->get('combyna.client_provider');
