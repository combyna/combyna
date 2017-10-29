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

use Combyna\CombynaBootstrap;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use InvalidArgumentException;

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

    $appState = $app->createInitialState();

    return function ($command, array $args = []) use ($app, &$appState, $arrayRenderer) {
        switch ($command) {
            case 'dispatchEvent':
                $appState = $app->dispatchEvent(
                    $appState,
                    $appState->getWidgetStatePathByPath($args[0]),
                    'gui',
                    'click',
                    [
                        // FIXME: Pass these in from the event data
                        'x' => 200,
                        'y' => 100
                    ]
                );
                break;
            case 'renderVisibleViews':
                return $arrayRenderer->renderViews($appState);
            default:
                throw new InvalidArgumentException(sprintf('Unsupported command "%s"', $command));
        }
    };
};
