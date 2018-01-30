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
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

if (preg_match('/\.(?:js|map)$/', $_SERVER['REQUEST_URI'])) {
    return false; // Serve the static file
}

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

$routeCollection = new RouteCollection();
$routeCollection->add('app_list', new Route('/', [
    '_type' => 'app_list'
]));

// TODO: Fetch these from the directory list under example/,
//       and fetch their titles etc. from the app configs themselves
$routeCollection->add('simple_app', new Route('/simple', [
    '_type' => 'app',
    '_appName' => 'simple',
    '_appTitle' => 'Combyna simple app'
]));

$requestContext = new RequestContext('/');
$matcher = new UrlMatcher($routeCollection, $requestContext);

$parameters = $matcher->match($_SERVER['REQUEST_URI']);

if ($parameters['_type'] === 'app_list') {
    print <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Combyna example apps</title>
    </head>
    <body>
        <h1>Combyna example apps</h1>

        <ul>
            <li><a href="/simple">Simple app</a></li>
        </ul>
    </body>
</html>
HTML;
    return;
}

$appName = $parameters['_appName'];
$appTitle = $parameters['_appTitle'];
$appPath = __DIR__ . '/' . $appName;

$combynaBootstrap = new CombynaBootstrap();
$container = $combynaBootstrap->getContainer(false);
/** @var Combyna $combyna */
$combyna = $container->get('combyna');
/** @var HtmlRenderer $htmlRenderer */
$htmlRenderer = $container->get('combyna.renderer.html');
/** @var YamlParser $yamlParser */
$yamlParser = $container->get('combyna.config.yaml_parser');

$environmentConfig = $yamlParser->parse(file_get_contents($appPath . '/environment.env.cyn.yml'));
$appConfig = $yamlParser->parse(file_get_contents($appPath . '/' . $appName . 'App.cyn.yml'));

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
        <title>$appTitle</title>
    </head>
    <body>
        <h1>$appTitle</h1>

        <div id="app">
            $renderedHtml
        </div>

        <script type="text/x-json" id="appConfig">$fullConfigJson</script>

        <script src="dist/client.js"></script>
    </body>
</html>
HTML;
