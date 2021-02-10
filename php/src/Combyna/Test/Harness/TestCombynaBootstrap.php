<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Test\Harness;

use Combyna\CombynaBootstrap;
use Combyna\CombynaBootstrapInterface;
use Combyna\Component\Framework\Bootstrap\BootstrapConfigInterface;
use Combyna\Component\Plugin\PluginInterface;
use Combyna\Plugin\Gui\GuiPlugin;
use Combyna\Test\Ui\TestGuiWidgetProviders;

/**
 * Class TestCombynaBootstrap
 *
 * Bootstraps a test framework with the provided plugins
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestCombynaBootstrap implements CombynaBootstrapInterface
{
    /**
     * @var CombynaBootstrapInterface
     */
    private $combynaBootstrap;

    /**
     * @param PluginInterface[] $plugins
     * @param string|null $rootPath
     * @param string|null $relativeCachePath
     * @param string|null $compiledContainerNamespace
     * @param string|null $compiledContainerClass
     */
    public function __construct(
        array $plugins = [],
        $rootPath = null,
        $relativeCachePath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    ) {
        $hasGuiPlugin = false;

        foreach ($plugins as $plugin) {
            if ($plugin instanceof GuiPlugin) {
                $hasGuiPlugin = true;
            }
        }

        if (!$hasGuiPlugin) {
            // Install the basic GUI plugin for integrated tests to use if not present
            $plugins[] = new GuiPlugin();
        }

        $this->combynaBootstrap = new CombynaBootstrap(
            $plugins,
            null,
            false,
            $rootPath,
            $relativeCachePath,
            $compiledContainerNamespace,
            $compiledContainerClass
        );
    }

    /**
     * Creates a new TestCombynaBootstrap from the provided BootstrapConfig
     *
     * @param BootstrapConfigInterface $bootstrapConfig
     * @param PluginInterface[] $additionalPlugins
     * @return self
     */
    public static function fromBootstrapConfig(
        BootstrapConfigInterface $bootstrapConfig,
        array $additionalPlugins = []
    ) {
        return new self(
            array_merge(
                $bootstrapConfig->getPlugins(),
                $additionalPlugins
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createContainer()
    {
        $container = $this->combynaBootstrap->createContainer();

        $testGuiWidgetProviders = new TestGuiWidgetProviders($container->get('combyna.expression.static_factory'));
        $container->get('combyna.environment.event_listener.widget_value_provider_installer')->addProvider(
            $testGuiWidgetProviders
        );
        // Make the test provider-provider available for stubbing
        $container->set('combyna_test.gui_widget_providers', $testGuiWidgetProviders);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommonCachePath()
    {
        return $this->combynaBootstrap->getCommonCachePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerBuilder()
    {
        return $this->combynaBootstrap->getContainerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerCachePath()
    {
        return $this->combynaBootstrap->getContainerCachePath();
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp()
    {
        $this->combynaBootstrap->warmUp();
    }
}
