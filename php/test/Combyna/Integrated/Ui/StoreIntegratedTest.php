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
use Combyna\Test\Ui\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StoreIntegratedTest
 *
 * Tests the UI "store" feature, used for storing data in "slots", making it available
 * via queries and registering signal handlers to change its stored data in response to signals
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StoreIntegratedTest extends TestCase
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

    /**
     * @var TestGuiWidgetProviders
     */
    private $testGuiWidgetProviders;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->testGuiWidgetProviders = $this->container->get('combyna_test.gui_widget_providers');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/storeTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
                // Test store queries with no parameters
            '    The age: "4"' .
                // Test store queries with parameters
                'Double the age: "8"' .
                '<input name="combyna-widget-my_view-root-2" type="text" value="4">' .
                '<button name="combyna-widget-my_view-root-3">Save new age</button>' .
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppShouldNotChangeItsOutputAfterChangingTheNewAgeTextboxButBeforeClickingSave()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-2':
                        return '100';
                    default:
                        return '';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
                // Test store queries with no parameters
            '    The age: "4"' .
                // Test store queries with parameters
                'Double the age: "8"' .
                '<input name="combyna-widget-my_view-root-2" type="text" value="4">' .
                '<button name="combyna-widget-my_view-root-3">Save new age</button>' .
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppDoesChangeItsOutputAfterChangingTheNewAgeTextboxAndClickingSave()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-2':
                        return '100';
                    default:
                        return '';
                }
            }
        );

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('my_app.save_new_age_button'),
            'gui',
            'click',
            [
                'x' => 100,
                'y' => 30
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
                // Test store queries with no parameters
            '    The age: "100"' .
                // Test store queries with parameters
                'Double the age: "200"' .
                '<input name="combyna-widget-my_view-root-2" type="text" value="4">' .
                '<button name="combyna-widget-my_view-root-3">Save new age</button>' .
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }
}
