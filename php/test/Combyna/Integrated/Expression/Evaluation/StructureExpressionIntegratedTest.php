<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression\Evaluation;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StructureExpressionIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpressionIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/structureExpressionTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            '    <input name="combyna-widget-my_view-root-0" type="text" value="Some default text">' .

            'First attribute has this value: Some default text' .

            # Check that an optional attribute of a capture with a structure type
            # does not need to have a value specified when the structure is set -
            # in which case, its default value will be evaluated and used
            'Third attribute has this value: 9876' .

            # Check that an attribute of a structure literal can be immediately dereferenced inline
            'Immediate attribute has this value: 21' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterChangingTheContentsOfTheTextbox()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-0':
                        return 'My edited text';
                    default:
                        return 'Some default text';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .

            // FIXME: HTML renderer should output the current/last value from provider here
            '    <input name="combyna-widget-my_view-root-0" type="text" value="Some default text">' .

            'First attribute has this value: My edited text' .

            'Third attribute has this value: 9876' .

            'Immediate attribute has this value: 21' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
