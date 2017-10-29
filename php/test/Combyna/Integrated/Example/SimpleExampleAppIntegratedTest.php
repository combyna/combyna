<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui;

use Combyna\CombynaBootstrap;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
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
     * @var CombynaBootstrap
     */
    private $combynaBootstrap;

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
        $this->combynaBootstrap = new CombynaBootstrap();
        $this->container = $this->combynaBootstrap->getContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../../../../../example/simple/simpleApp.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="list">
    <div><input name="combyna-widget-list-root-contents-0" type="text" value="Click a button"><button name="combyna-widget-list-root-contents-1">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-2">Set text to "Goodbye!"</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
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
    <div><input name="combyna-widget-list-root-contents-0" type="text" value="Goodbye!"><button name="combyna-widget-list-root-contents-1">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-2">Set text to "Goodbye!"</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingAButtonLocatedByPath()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->navigateTo($appState, 'app', 'list');

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByPath(['list', 'root', 'contents', 2]),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="list">
    <div><input name="combyna-widget-list-root-contents-0" type="text" value="Goodbye!"><button name="combyna-widget-list-root-contents-1">Set text to "Hello!"</button><button name="combyna-widget-list-root-contents-2">Set text to "Goodbye!"</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
