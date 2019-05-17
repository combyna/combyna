<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\App;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SimpleExampleAppIntegratedTest
 *
 * Automates the simple example app to check for correct behaviour
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleExampleAppIntegratedTest extends TestCase
{
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnvironmentNode
     */
    private $environment;

    /**
     * @var HtmlRenderer
     */
    private $htmlRenderer;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/simpleApp.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="list">
    <div>Click one of the buttons to change the text: <input name="combyna-widget-list-root-contents-1" type="text" value="Click a button"><button name="combyna-widget-list-root-contents-2">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-3">Set text to "Goodbye!"</button></div>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingAButtonLocatedByTag()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->navigateTo($appState, 'app', 'list');

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('list.second_set_text_button'),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="list">
    <div>Click one of the buttons to change the text: <input name="combyna-widget-list-root-contents-1" type="text" value="Goodbye!"><button name="combyna-widget-list-root-contents-2">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-3">Set text to "Goodbye!"</button></div>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingAButtonLocatedByPath()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->navigateTo($appState, 'app', 'list');

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByPath(['list', 'root', 'contents', 3]),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="list">
    <div>Click one of the buttons to change the text: <input name="combyna-widget-list-root-contents-1" type="text" value="Goodbye!"><button name="combyna-widget-list-root-contents-2">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-3">Set text to "Goodbye!"</button></div>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
