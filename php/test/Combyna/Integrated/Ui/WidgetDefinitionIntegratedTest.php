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
use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetDefinitionIntegratedTest
 *
 * Tests the UI "widget definition" feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionIntegratedTest extends TestCase
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

        $delegatingWidgetRenderer = $this->container->get('combyna.renderer.html.widget');

        $delegatingWidgetRenderer->addWidgetRenderer(
            new class ($delegatingWidgetRenderer) implements WidgetRendererInterface {
                /**
                 * @var DelegatingWidgetRenderer
                 */
                private $delegatingWidgetRenderer;

                /**
                 * @param DelegatingWidgetRenderer $delegatingWidgetRenderer
                 */
                public function __construct(DelegatingWidgetRenderer $delegatingWidgetRenderer)
                {
                    $this->delegatingWidgetRenderer = $delegatingWidgetRenderer;
                }

                /**
                 * @return string
                 */
                public function getWidgetDefinitionLibraryName()
                {
                    return 'app';
                }

                /**
                 * @return string
                 */
                public function getWidgetDefinitionName()
                {
                    return 'my_primitive_widget';
                }

                /**
                 * @param WidgetStateInterface $widgetState
                 * @param WidgetStatePathInterface $widgetStatePath
                 * @return HtmlNodeInterface
                 */
                public function renderWidget(WidgetStateInterface $widgetState, WidgetStatePathInterface $widgetStatePath)
                {
                    if (!$widgetState instanceof DefinedWidgetStateInterface) {
                        throw new InvalidArgumentException('Wrong type of widget state');
                    }

                    $childNodes = [
                        new TextNode('Label :: ' . $widgetState->getAttribute('primitives_label')->toNative()),
                        $this->delegatingWidgetRenderer->renderWidget(
                            $widgetStatePath->getChildStatePath('primitives_child')
                        )
                    ];
                    $htmlAttributes = [];

                    return new RenderedWidget(
                        $widgetState,
                        new HtmlElement('section', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, $childNodes)
                    );
                }
            }
        );

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->testGuiWidgetProviders = $this->container->get('combyna_test.gui_widget_providers');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetDefinitionTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    <section>' .
                    // Test that the attribute is passed through and concatenated correctly
                    'Label :: Some text for my label: Hello from the label attr! (my suffix)' .

                    // Test that the child widget passed to the compound one makes its way through
                    'Hello from the child widget!' .
                '</section>' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
