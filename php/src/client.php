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

use Combyna\Client\ClientFactory;
use Combyna\CombynaBootstrap;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Plugin\Bootstrap\BootstrapPlugin;

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

$combynaBootstrap = new CombynaBootstrap([
    new BootstrapPlugin()
]);

$container = $combynaBootstrap->getContainer(false);
/** @var Combyna $combyna */
$combyna = $container->get('combyna');
/** @var ArrayRenderer $arrayRenderer */
$arrayRenderer = $container->get('combyna.renderer.array');

return new ClientFactory($combyna, $arrayRenderer);
