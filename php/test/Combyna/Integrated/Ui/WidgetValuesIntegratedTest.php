<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Ui\Fixtures\PokableButtonValueProviders;
use Combyna\Integrated\Ui\Fixtures\PokableButtonWidgetRenderer;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetValuesIntegratedTest
 *
 * Tests the widget "value" feature, used for fetching eg. the text value of a textbox input field
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValuesIntegratedTest extends TestCase
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

        $this->container->get('combyna.renderer.html.widget')->addWidgetRenderer(new PokableButtonWidgetRenderer());

        $this->container->get('combyna.environment.event_listener.widget_value_provider_installer')->addProvider(
            new PokableButtonValueProviders($this->container->get('combyna.expression.static_factory'))
        );

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetValuesTest.env.cyn.yml'));
        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetValuesTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment($environmentConfig);
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    Value of the pokable button: ""<button name="combyna-widget-my_view-root-1">My pokable button</button>Value of the addable button: "0"<button name="combyna-widget-my_view-root-3-root">Add me</button>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterPokingThePokableButton()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('the_pokable_button'),
            'widget_values',
            'poked'
        );

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    Value of the pokable button: "Bang: (my_view-root-1-1)"<button name="combyna-widget-my_view-root-1">My pokable button</button>Value of the addable button: "0"<button name="combyna-widget-my_view-root-3-root">Add me</button>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingTheAddableButton()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('the_addable_button'),
            'gui',
            'click'
        );

        // Note the `Value of the addable button: "61"`
        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    Value of the pokable button: ""<button name="combyna-widget-my_view-root-1">My pokable button</button>Value of the addable button: "61"<button name="combyna-widget-my_view-root-3-root">Add me</button>
</div>
HTML;
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
