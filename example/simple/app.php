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
use Combyna\Component\Config\YamlParser;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;

if (preg_match('/\.(?:js|map)$/', $_SERVER['REQUEST_URI'])) {
    return false; // Serve the static file
}

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

$combynaBootstrap = new CombynaBootstrap();
$container = $combynaBootstrap->getContainer(false);
/** @var Combyna $combyna */
$combyna = $container->get('combyna');
/** @var HtmlRenderer $htmlRenderer */
$htmlRenderer = $container->get('combyna.renderer.html');
/** @var YamlParser $yamlParser */
$yamlParser = $container->get('combyna.config.yaml_parser');

$environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/environment.env.cyn.yml'));
$appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/simpleApp.cyn.yml'));

$environment = $combyna->createEnvironment($environmentConfig);
$app = $combyna->createApp($appConfig, $environment);

$appState = $app->createInitialState();

//$appState = $app->dispatchEvent(
//    $appState,
//    $appState->getWidgetStatePathByTag('list.second_set_text_button'),
//    'gui',
//    'click',
//    [
//        'x' => 200,
//        'y' => 100
//    ]
//);

$renderedHtml = $htmlRenderer->renderApp($appState);

$fullConfigJson = json_encode([
    'environment' => $environmentConfig,
    'app' => $appConfig
]);

print <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Combyna demo</title>
    </head>
    <body>
        <h1>Combyna "simple" demo</h1>

        <div id="app">
            $renderedHtml
        </div>

        <script type="text/x-json" id="appConfig">$fullConfigJson</script>

        <script src="dist/client.js"></script>
    </body>
</html>
HTML;
