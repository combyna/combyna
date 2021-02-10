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
use Combyna\Component\App\Exception\EventDispatchFailedException;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Concise\Core\TestCase;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetEventsIntegratedTest
 *
 * Tests the widget "event" feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetEventsIntegratedTest extends TestCase
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
                 * {@inheritdoc}
                 */
                public function getWidgetDefinitionLibraryName()
                {
                    return 'widget_events';
                }

                /**
                 * {@inheritdoc}
                 */
                public function getWidgetDefinitionName()
                {
                    return 'switchable_thing';
                }

                /**
                 * {@inheritdoc}
                 */
                public function renderWidget(
                    WidgetStateInterface $widgetState,
                    WidgetStatePathInterface $widgetStatePath,
                    ProgramInterface $program
                ) {
                    if (!$widgetState instanceof DefinedWidgetStateInterface) {
                        throw new InvalidArgumentException('Wrong type of widget state');
                    }

                    $childNodes = [
                        new TextNode('Label :: ' . $widgetState->getAttribute('label')->toNative())
                    ];
                    $htmlAttributes = [];

                    return new RenderedWidget(
                        $widgetState,
                        new HtmlElement('div', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, $childNodes)
                    );
                }
            }
        );

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetEventsTest.env.cyn.yml'));
        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetEventsTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment($environmentConfig);
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            // Test that no event is fired initially
            '    How the thing was switched: "(Not yet switched)"' .
                '<div>Label :: Switch me!</div>' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterSwitchingTheSwitchableThing()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('the_switchable_thing'),
            'widget_events',
            'switched',
            [
                'how' => [
                    'myInnerValue' => 'To the left'
                ]
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            // Test that the event is dispatched correctly along with its payload (the "To the left" string from above)
            '    How the thing was switched: "To the left"' .
                '<div>Label :: Switch me!</div>' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testExceptionIsThrownWhenAnUnreferencedEventIsDispatchedForTheSwitchableThingWidget()
    {
        $appState = $this->app->createInitialState();

        $this->setExpectedException(
            EventDispatchFailedException::class,
            'Event definition "an_invalid_event" for library "widget_events" is not referenced by widget "switchable_thing" for library "widget_events"'
        );

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('the_switchable_thing'),
            'widget_events',
            'an_invalid_event',
            [
                'an' => 'invalid payload value'
            ]
        );
    }

    public function testExceptionIsThrownWhenEventWithInvalidPayloadIsDispatchedForTheSwitchableThingWidget()
    {
        $appState = $this->app->createInitialState();

        $this->setExpectedException(
            EventDispatchFailedException::class,
            'Native value for required static "how" is missing from array'
        );

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('the_switchable_thing'),
            'widget_events',
            'switched',
            [
                'an' => 'invalid payload value'
            ]
        );
    }
}
