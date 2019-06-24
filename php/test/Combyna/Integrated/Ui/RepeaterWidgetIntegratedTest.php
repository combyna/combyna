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
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RepeaterWidgetIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/repeaterWidgetTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppRendersTheListOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="item_viewer">' .
            "\n" .
            '    Selected item: (No item selected)' . // Check that initially, no item is selected
                '<hr>' .
                'Items: ' .
                '<button name="combyna-widget-item_viewer-root-3-0-0">Select item "first"</button>' .
                '<button name="combyna-widget-item_viewer-root-3-1-0">Select item "second"</button>' .
                '<button name="combyna-widget-item_viewer-root-3-2-0">Select item "third"</button>' .
                "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }

    public function testRenderAppDisplaysTheSelectedItemAfterSelecting()
    {
        $appState = $this->app->createInitialState();

        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathsByTag('list_view.select_item_button')[1],
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml =
            '<div class="combyna-view" data-view-name="item_viewer">' .
            "\n" .
            '    Selected item: second' . // Check that the selected item is displayed after selecting
                '<hr>' .
                'Items: ' .
                '<button name="combyna-widget-item_viewer-root-3-0-0">Select item "first"</button>' .
                '<button name="combyna-widget-item_viewer-root-3-1-0">Select item "second"</button>' .
                '<button name="combyna-widget-item_viewer-root-3-2-0">Select item "third"</button>' .
            "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }
}
