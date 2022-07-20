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
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CompoundWidgetIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetIntegratedTest extends TestCase
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

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/compoundWidgetTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppRendersCorrectlyOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    Outer text: OUTER Inner text: Hi inner, from outer (OUTER)! HI FROM OUTER' .
                "\n" .
            '</div>';
        static::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState, $this->app));
    }
}
