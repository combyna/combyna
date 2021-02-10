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
use Combyna\Test\Ui\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NonZeroAssuranceIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroAssuranceIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/nonZeroAssuranceTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // Dividend textbox
            '    <input name="combyna-widget-my_view-root-1" type="text" value="100">' .

            '/' .

            // Divisor textbox
            '<input name="combyna-widget-my_view-root-3" type="text" value="5">' .

            // 100/5 should be 20
            'The quotient is: 20.' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterEditingTheDividendTextbox()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-1':
                        return '20';
                    default:
                        return '5';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // Dividend textbox (value attr will be unchanged, even though value has changed)
            '    <input name="combyna-widget-my_view-root-1" type="text" value="100">' .

            '/' .

            // Divisor textbox
            '<input name="combyna-widget-my_view-root-3" type="text" value="5">' .

            // 20/5 should be 4
            'The quotient is: 4.' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterEditingTheDivisorTextboxToBeZero()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-3':
                        return '0';
                    default:
                        return '100';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // Dividend textbox
            '    <input name="combyna-widget-my_view-root-1" type="text" value="100">' .

            '/' .

            // Divisor textbox (value attr will be unchanged, even though value has changed)
            '<input name="combyna-widget-my_view-root-3" type="text" value="5">' .

            // Division by zero is undefined, so the alternate expression should be displayed
            'The divisor is zero, so the quotient cannot be determined' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }
}
