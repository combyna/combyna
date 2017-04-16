<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

function is_bool($value) {
    return true;
}
function is_float($value) {
    return true;
}
function is_int($value) {
    return true;
}
function is_string($value) {
    return true;
}

use Combyna\Combyna;
use Combyna\CombynaBootstrap;
use Combyna\Component\Renderer\Html\ArrayRenderer;

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

$combynaBootstrap = new CombynaBootstrap();
$container = $combynaBootstrap->getContainer(false);
/** @var Combyna $combyna */
$combyna = $container->get('combyna');
/** @var ArrayRenderer $arrayRenderer */
$arrayRenderer = $container->get('combyna.renderer.array');

return function (array $environmentConfig, array $appConfig) use ($combyna, $arrayRenderer) {
    $environment = $combyna->createEnvironment($environmentConfig);

    $app = $combyna->createApp($appConfig, $environment);

    return function ($viewName, array $viewAttributes = []) use ($app, $arrayRenderer) {
        $renderedView = $app->renderView($viewName);
        $renderedViewArray = $arrayRenderer->renderView($renderedView);

        return $renderedViewArray;
    };
};
