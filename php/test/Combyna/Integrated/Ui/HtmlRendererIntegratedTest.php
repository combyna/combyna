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

use Combyna\CombynaBootstrap;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HtmlRendererIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlRendererIntegratedTest extends TestCase
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
     * @var CombynaBootstrap
     */
    private $combynaBootstrap;

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
        $this->combynaBootstrap = new CombynaBootstrap();
        $this->container = $this->combynaBootstrap->getContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');

        $this->environment = $this->combyna->createEnvironment([
//            'libraries' => [
//                [
//                    'name' => 'gui',
//                    'description' => 'GUI tools',
//                    'widgets' => [
//                        'button' => [
//                            'type' => 'core',
//                            'attributes' => [
//                                'label' => 'text'
//                            ],
//                            'children' => []
//                        ]
//                    ]
//                ]
//            ]
        ]);
    }

    public function testRenderViewReturnsTheCorrectHtmlWhenAppHasNoLogic()
    {
        $this->app = $this->combyna->createApp([
            'name' => 'My test Combyna app',
            'translations' => [],
            'home' => [
                'route' => 'app.my_home_route'
            ],
            'routes' => [
                'my_home_route' => [
                    'pattern' => '',
                    'page_view' => 'my_view'
                ]
            ],
            'page_views' => [
                'my_view' => [
                    'title' => [
                        'type' => 'text',
                        'text' => 'My view'
                    ],
                    'description' => 'A test view, for testing',
                    'widget' => [
                        'type' => 'gui.button',
                        'attributes' => [
                            'label' => [
                                'type' => 'text',
                                'text' => 'Click me'
                            ]
                        ],
                        'children' => null
                    ],
                    'store' => null
                ]
            ]
        ], $this->environment);

        $appState = $this->app->createInitialState();
        $renderedHtml = $this->htmlRenderer->renderApp($appState);

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    <button name="combyna-widget-my_view-root">Click me</button>
</div>
HTML;
        $this->assertSame($expectedHtml, $renderedHtml);
    }
}
