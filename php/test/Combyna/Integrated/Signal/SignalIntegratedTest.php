<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Signal;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Signal\EventDispatcher\Event\SignalDispatchedEvent;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SignalIntegratedTest
 *
 * Tests the signal feature both when marked as broadcast and when not
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/signalTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    <button name="combyna-widget-my_view-root-0">Dispatch implicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-1">Dispatch explicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-2">Dispatch broadcaster</button>' .

                '(Nothing has been dispatched yet)' . // Current text display
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingTheImplicitNonBroadcasterButton()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_implicit_non_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    <button name="combyna-widget-my_view-root-0">Dispatch implicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-1">Dispatch explicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-2">Dispatch broadcaster</button>' .

                'From the implicit non-broadcaster' . // Current text display
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testDispatchEventEmitsNoSignalEventAfterClickingTheImplicitNonBroadcasterButton()
    {
        $lastEvent = null;
        $this->combyna->onBroadcastSignal(function (SignalDispatchedEvent $event) use (&$lastEvent) {
            $lastEvent = $event;
        });
        $appState = $this->app->createInitialState();

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_implicit_non_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        static::assertNull($lastEvent);
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingTheExplicitNonBroadcasterButton()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_explicit_non_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    <button name="combyna-widget-my_view-root-0">Dispatch implicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-1">Dispatch explicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-2">Dispatch broadcaster</button>' .

                'From the explicit non-broadcaster' . // Current text display
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testDispatchEventEmitsNoSignalEventAfterClickingTheExplicitNonBroadcasterButton()
    {
        $lastEvent = null;
        $this->combyna->onBroadcastSignal(function (SignalDispatchedEvent $event) use (&$lastEvent) {
            $lastEvent = $event;
        });
        $appState = $this->app->createInitialState();

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_explicit_non_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        static::assertNull($lastEvent);
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterClickingTheBroadcasterButton()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    <button name="combyna-widget-my_view-root-0">Dispatch implicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-1">Dispatch explicit non-broadcaster</button>' .
                '<button name="combyna-widget-my_view-root-2">Dispatch broadcaster</button>' .

                'From the broadcaster' . // Current text display
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testDispatchEventEmitsASignalEventAfterClickingTheBroadcasterButton()
    {
        $lastEvent = null;
        $this->combyna->onBroadcastSignal(function (SignalDispatchedEvent $event) use (&$lastEvent) {
            $lastEvent = $event;
        });
        $appState = $this->app->createInitialState();

        $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('dispatch_broadcaster_button'),
            'gui',
            'click',
            [
                'x' => 0,
                'y' => 0
            ]
        );

        static::assertInstanceOf(SignalDispatchedEvent::class, $lastEvent);
    }
}
