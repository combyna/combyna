<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Config;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Config\Loader\ConfigBuilder;
use Combyna\Component\Config\Loader\ApplicatonLoader;
use Combyna\Component\Config\Loader\YamlFileLoader;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StructuredConfigIntegratedTest
 *
 * Tests the include expression node visitor feature
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
class StructuredConfigIntegratedTest extends TestCase
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

        $config = new ConfigBuilder();
        $locator = new FileLocator([__DIR__ . '/Fixtures']);
        $appConfigLoader = new ApplicatonLoader($config, $locator);
        $appConfigLoader->setResolver(new LoaderResolver([
            new YamlFileLoader($config, $yamlParser, $locator),
            $appConfigLoader
        ]));
         $appConfigLoader->load('app/');

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($config->getConfig(), $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();
    }

}
