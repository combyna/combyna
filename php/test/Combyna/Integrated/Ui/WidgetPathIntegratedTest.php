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
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetPathIntegratedTest
 *
 * Tests the UI widget "path" feature, used for uniquely referencing a widget instance
 * in the current state of the UI (as passed in)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetPathIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetPathTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testTextWidgetPathShouldBeFetchableByRawPath()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 0],
            $appState->getWidgetStatePathByPath(['my_entry_page', 'root', 'contents', 0])
                ->getWidgetStatePath()
        );
    }

    public function testTextWidgetPathShouldBeFetchableByTag()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 0],
            $appState->getWidgetStatePathByTag('my_text_widget')->getWidgetStatePath()
        );
    }

    public function testRepeaterWidgetPathShouldBeFetchableByRawPath()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 1],
            $appState->getWidgetStatePathByPath(['my_entry_page', 'root', 'contents', 1])
                ->getWidgetStatePath()
        );
    }

    public function testRepeaterWidgetPathShouldBeFetchableByTag()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 1],
            $appState->getWidgetStatePathByTag('my_repeater_widget')->getWidgetStatePath()
        );
    }

    public function testRepeaterWidgetDescendantPathShouldBeFetchableByRawPath()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 1, 2],
            $appState->getWidgetStatePathByPath(['my_entry_page', 'root', 'contents', 1, 2])
                ->getWidgetStatePath()
        );
    }

    public function testRepeaterWidgetDescendantPathsShouldBeFetchableByTag()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            [
                ['my_entry_page', 'root', 'contents', 1, 0],
                ['my_entry_page', 'root', 'contents', 1, 1],
                ['my_entry_page', 'root', 'contents', 1, 2]
            ],
            array_map(function (WidgetStatePathInterface $path) {
                return $path->getWidgetStatePath();
            }, $appState->getWidgetStatePathsByTag('my_descendant_of_repeater_widget'))
        );
    }

    public function testConditionalWidgetPathShouldBeFetchableByRawPath()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 2],
            $appState->getWidgetStatePathByPath(['my_entry_page', 'root', 'contents', 2])
                ->getWidgetStatePath()
        );
    }

    public function testConditionalWidgetPathShouldBeFetchableByTag()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 2],
            $appState->getWidgetStatePathByTag('my_conditional_widget')->getWidgetStatePath()
        );
    }

    public function testConditionalWidgetDescendantPathShouldBeFetchableByRawPath()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 2, 'consequent'],
            $appState->getWidgetStatePathByPath(['my_entry_page', 'root', 'contents', 2, 'consequent'])
                ->getWidgetStatePath()
        );
    }

    public function testConditionalWidgetDescendantPathShouldBeFetchableByTag()
    {
        $appState = $this->app->createInitialState();

        self::assertEquals(
            ['my_entry_page', 'root', 'contents', 2, 'consequent'],
            $appState->getWidgetStatePathByTag('my_descendant_of_conditional_widget')
                ->getWidgetStatePath()
        );
    }
}
