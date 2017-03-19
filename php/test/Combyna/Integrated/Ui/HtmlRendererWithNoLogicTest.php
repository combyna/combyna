<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui;

use Combyna\Combyna;
use Combyna\CombynaBootstrap;
use Combyna\Component\App\App;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HtmlRendererWithNoLogicTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlRendererWithNoLogicTest extends TestCase
{
    /**
     * @var App
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
            'libraries' => [
                [
                    'name' => 'gui',
                    'description' => 'GUI tools',
                    'widgets' => [
                        'button' => [
                            'type' => 'core',
                            'attributes' => [
                                'label' => 'text'
                            ],
                            'children' => []
                        ]
                    ]
                ]
            ]
        ]);

        $this->app = $this->combyna->createApp([
            'name' => 'My test Combyna app',
            'translations' => [],
            'views' => [
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
    }

    public function testRenderViewReturnsTheCorrectHtml()
    {
        $renderedView = $this->app->renderView('my_view');
        $renderedHtml = $this->htmlRenderer->renderView($renderedView);

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    <button>Click me</button>
</div>
HTML;
        $this->assertSame($expectedHtml, $renderedHtml);
    }
}
