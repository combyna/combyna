<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui\HtmlRenderer;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BasicHtmlRendererIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BasicHtmlRendererIntegratedTest extends TestCase
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
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $this->container = $combynaBootstrap->getContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');

        $this->environment = $this->combyna->createEnvironment();
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
                        'type' => 'group',
                        'children' => [
                            [
                                // Demonstrate text widgets
                                'type' => 'text',
                                'text' => [
                                    // Just to prove that expressions work
                                    'type' => 'concatenation',
                                    'list' => [
                                        'type' => 'list',
                                        'elements' => [
                                            [
                                                'type' => 'text',
                                                'text' => 'Some'
                                            ],
                                            [
                                                'type' => 'text',
                                                'text' => 'here '
                                            ]
                                        ]
                                    ],
                                    'glue' => [
                                        'type' => 'text',
                                        'text' => ' of my text '
                                    ]
                                ]
                            ],
                            [
                                'type' => 'gui.button',
                                'attributes' => [
                                    'label' => [
                                        'type' => 'text',
                                        'text' => 'Click me'
                                    ]
                                ],
                                'children' => null
                            ]
                        ]
                    ],
                    'store' => null
                ]
            ]
        ], $this->environment);

        $appState = $this->app->createInitialState();
        $renderedHtml = $this->htmlRenderer->renderApp($appState);

        $expectedHtml = <<<HTML
<div class="combyna-view" data-view-name="my_view">
    Some of my text here <button name="combyna-widget-my_view-root-1">Click me</button>
</div>
HTML;
        $this->assertSame($expectedHtml, $renderedHtml);
    }
}
