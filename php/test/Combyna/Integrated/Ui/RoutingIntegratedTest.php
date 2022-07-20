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
use Combyna\Component\App\Exception\SignalDispatchFailedException;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Router\EventDispatcher\Event\RouteNavigatedEvent;
use Combyna\Component\Signal\EventDispatcher\Event\SignalDispatchedEvent;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Harness\TestCase;
use Combyna\Test\Ui\TestGuiWidgetProviders;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RoutingIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RoutingIntegratedTest extends TestCase
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
                    return 'routing_test';
                }

                /**
                 * @return string
                 */
                public function getWidgetDefinitionName()
                {
                    return 'navigable_thing';
                }

                /**
                 * @param WidgetStateInterface $widgetState
                 * @param WidgetStatePathInterface $widgetStatePath
                 * @param ProgramInterface $program
                 * @return HtmlNodeInterface
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
                        new HtmlElement('span', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, $childNodes)
                    );
                }
            }
        );

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->testGuiWidgetProviders = $this->container->get('combyna_test.gui_widget_providers');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/routingTest.env.cyn.yml'));
        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/routingTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment($environmentConfig);
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppRendersTheHomeRouteListPageViewOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_list_view">' .
            "\n" .
            '    <a href="/item/item_1234">See the example item</a>' .
                '<span>Label :: Navigate me</span>' .
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppRendersTheItemPageViewAfterFollowingTheViewItemLink()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            // This will actually dispatch from the gui.url_link widget _inside_ this compound gui.route_link one
            $appState->getWidgetStatePathByTag('list_view.view_item_button'),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_item_view">' .
            "\n" .
            '    The item slug is: item_1234' .
                '<a href="/">Go back home</a>' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppDispatchesTheRouteNavigationEventAfterFollowingTheViewItemLink()
    {
        $lastEvent = null;
        $this->combyna->onRouteNavigated(function (RouteNavigatedEvent $event) use (&$lastEvent) {
            $lastEvent = $event;
        });
        $appState = $this->app->createInitialState();

        $this->app->dispatchEvent(
            $appState,
            // This will actually dispatch from the gui.url_link widget _inside_ this compound gui.route_link one
            $appState->getWidgetStatePathByTag('list_view.view_item_button'),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        /** @var RouteNavigatedEvent $lastEvent */
        static::assertInstanceOf(RouteNavigatedEvent::class, $lastEvent);
        static::assertSame('/item/item_1234', $lastEvent->getUrl());
    }

    public function testRenderAppDispatchesTheBroadcastSignalAfterNavigatingTheNavigableThingToTheItemPage()
    {
        $lastEvent = null;
        $this->combyna->onBroadcastSignal(function (SignalDispatchedEvent $event) use (&$lastEvent) {
            $lastEvent = $event;
        });
        $appState = $this->app->createInitialState();

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('list_view.navigable_thing'),
            'routing_test',
            'somehow_navigated',
            [
                'route' => 'app.my_item_route',
                'arguments' => [
                    'my_item_slug' => 'item_1234'
                ]
            ]
        );

        /** @var SignalDispatchedEvent $lastEvent */
        static::assertInstanceOf(SignalDispatchedEvent::class, $lastEvent);
        static::assertSame('app', $lastEvent->getSignal()->getLibraryName());
        static::assertSame('navigation_detected', $lastEvent->getSignal()->getName());
        static::assertSame(
            'app.my_item_route',
            $lastEvent->getSignal()->getPayloadStatic('which_route')->toNative()
        );
    }

    public function testRenderAppRendersTheAboutPageCorrectlyBeforeAnySignalIsDispatched()
    {
        $appState = $this->app->createInitialState();
        $appState = $this->app->navigateTo(
            $appState,
            'app',
            'my_about_route'
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_about_view">' .
            "\n" .
            '    <a href="/">Go back home</a>' .
                'Provided URL: (None)' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testTheAboutPageAllowsADynamicRouteAndArgumentsToBeProvidedViaSignal()
    {
        $appState = $this->app->createInitialState();
        $appState = $this->app->navigateTo(
            $appState,
            'app',
            'my_about_route'
        );

        // Dispatch a signal whose payload will be coerced by the exotic route name and arguments types
        // in the payload to ensure they reference a valid route and a valid set of arguments for that route
        $appState = $this->app->dispatchSignal(
            $appState,
            'app',
            'provide_some_route_data',
            [
                'route' => 'app.my_item_route',
                'arguments' => [
                    'my_item_slug' => 'item_123456'
                ]
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_about_view">' .
            "\n" .
            '    <a href="/">Go back home</a>' .
                'Provided URL: /item/item_123456' .
            "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testExceptionIsThrownOnAboutPageWhenInvalidDynamicRouteAndArgumentsProvidedViaSignal()
    {
        $appState = $this->app->createInitialState();
        $appState = $this->app->navigateTo(
            $appState,
            'app',
            'my_about_route'
        );

        $this->expectException(SignalDispatchFailedException::class);
        $this->expectExceptionMessage(
            'Route "an_invalid_route" for library "app" does not exist'
        );

        // Dispatch a signal whose payload will be coerced by the exotic route name and arguments types
        // in the payload to ensure they reference a valid route and a valid set of arguments for that route
        $this->app->dispatchSignal(
            $appState,
            'app',
            'provide_some_route_data',
            [
                'route' => 'app.an_invalid_route',
                'arguments' => [
                    'an_invalid_argument' => 'an invalid value'
                ]
            ]
        );
    }
}
