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

use Combyna\CombynaBootstrap;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ComplexWidgetTreeIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComplexWidgetTreeIntegratedTest extends TestCase
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
            'libraries' => [
                [
                    'name' => 'foobar',
                    'description' => 'A test library',
                    'dependencies' => ['gui'],
                    'widgets' => [
                        'labelled_button' => [
                            'type' => 'compound',
                            'attributes' => [
                                'the_label' => 'text',
                                'the_button_label' => 'text'
                            ],
                            'children' => [
                                'icon' => [
                                    'groups' => ['gui.primitive']
                                ]
                            ],
                            'root' => [
                                'type' => 'group',
                                'children' => [
                                    [
                                        // Demonstrate referencing a child of the compound widget
                                        'type' => 'child',
                                        'name' => 'icon'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => [
                                            'type' => 'builtin',
                                            'name' => 'widget_attr',
                                            'positional-arguments' => [
                                                ['type' => 'text', 'text' => 'the_label']
                                            ],
                                            'named-arguments' => []
                                        ]
                                    ],
                                    [
                                        // Nest the button widget inside another,
                                        // to check that attributes are only fetched from the compound widget
                                        'type' => 'gui.box',
                                        'children' => [
                                            'contents' => [
                                                // Demonstate a simple button from the GUI library
                                                'type' => 'gui.button',
                                                'attributes' => [
                                                    'label' => [
                                                        'type' => 'builtin',
                                                        'name' => 'widget_attr',
                                                        'positional-arguments' => [
                                                            ['type' => 'text', 'text' => 'the_button_label']
                                                        ],
                                                        'named-arguments' => []
                                                    ]
                                                ],
                                                'children' => []
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'events' => []
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testComplexWidgetTreesShouldBeRenderable()
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
                                                'text' => 'here - '
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
                                // Demonstrate a compound widget
                                'type' => 'foobar.labelled_button',
                                'attributes' => [
                                    'the_label' => [
                                        'type' => 'text',
                                        'text' => 'Click this button: '
                                    ],
                                    'the_button_label' => [
                                        'type' => 'text',
                                        'text' => 'Click me'
                                    ]
                                ],
                                'children' => [
                                    'icon' => [
                                        'type' => 'text',
                                        'text' => [
                                            'type' => 'text',
                                            'text' => '[¯\_(ツ)_/¯] '
                                        ]
                                    ]
                                ]
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
    Some of my text here - [¯\_(ツ)_/¯] Click this button: <div><button name="combyna-widget-my_view-root-1-root-2-contents">Click me</button></div>
</div>
HTML;
        $this->assertSame($expectedHtml, $renderedHtml);
    }
}
