<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\App;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EditableListExampleAppIntegratedTest
 *
 * Automates the editable list example app to check for correct behaviour
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EditableListExampleAppIntegratedTest extends TestCase
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
        $this->container = $combynaBootstrap->getContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->testGuiWidgetProviders = $this->container->get('combyna_test.gui_widget_providers');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/editableListApp.cyn.yml'));

//        $this->combyna->useProductionMode();
        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="item_viewer">
    <div>Items: <hr>Add another: <input name="combyna-widget-item_viewer-root-contents-4" type="text" value="&lt;Enter some text&gt;"><button name="combyna-widget-item_viewer-root-contents-5">Add item</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterAddingTwoNewItemsViaTheUi()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                return 'First: (' . implode('-', $widgetStatePath) . ')';
            }
        );
        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('item_viewer.add_item_button'),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );
        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                return 'Second: (' . implode('-', $widgetStatePath) . ')';
            }
        );
        $appState = $this->app->dispatchEvent(
            $appState,
            $appState->getWidgetStatePathByTag('item_viewer.add_item_button'),
            'gui',
            'click',
            [
                'x' => 200,
                'y' => 100
            ]
        );

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="item_viewer">
    <div>Items: <div>(1)First: (item_viewer-root-contents-4)</div><div>(2)Second: (item_viewer-root-contents-4)</div><hr>Add another: <input name="combyna-widget-item_viewer-root-contents-4" type="text" value="&lt;Enter some text&gt;"><button name="combyna-widget-item_viewer-root-contents-5">Add item</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
