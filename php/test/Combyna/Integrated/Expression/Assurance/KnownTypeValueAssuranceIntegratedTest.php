<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression\Assurance;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class KnownTypeValueAssuranceIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownTypeValueAssuranceIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/knownTypeValueAssuranceTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // The text was left not set to "40", so no doubling should happen
            '    <input name="combyna-widget-my_view-root-0" type="text" value="not a number">' .

            'The capture is not a number, so it cannot be doubled' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterSettingTheNumberTextboxTo40()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-0':
                        return '40';
                    default:
                        return '10';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // The text was changed to 40 (not reflected in state here) so doubling should have happened
            '    <input name="combyna-widget-my_view-root-0" type="text" value="not a number">' .

            'Double is: 80.' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterSettingTheNumberTextboxToADifferentNumber()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-0':
                        return '43';
                    default:
                        return '10';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // The text was left not set to "40", so no doubling should happen
            '    <input name="combyna-widget-my_view-root-0" type="text" value="not a number">' .

            'The capture is not a number, so it cannot be doubled' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
