<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Harness;

use Combyna\CombynaBootstrap;
use Combyna\CombynaBootstrapInterface;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Combyna\Plugin\Gui\GuiPlugin;

/**
 * Class TestCombynaBootstrap
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestCombynaBootstrap implements CombynaBootstrapInterface
{
    /**
     * @var CombynaBootstrap
     */
    private $combynaBootstrap;

    public function __construct()
    {
        $this->combynaBootstrap = new CombynaBootstrap([
            // Install the basic GUI plugin for integrated tests to use
            new GuiPlugin()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureContainer(
        $compiledContainerPath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    ) {
        $this->combynaBootstrap->configureContainer(
            $compiledContainerPath,
            $compiledContainerNamespace,
            $compiledContainerClass
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer($isDebug = true)
    {
        $container = $this->combynaBootstrap->getContainer(false);

        $testGuiWidgetProviders = new TestGuiWidgetProviders($container->get('combyna.expression.static_factory'));
        $container->get('combyna.environment.event_listener.widget_value_provider_installer')->addProvider(
            $testGuiWidgetProviders
        );
        // Make the test provider-provider available for stubbing
        $container->set('combyna_test.gui_widget_providers', $testGuiWidgetProviders);

        return $container;
    }
}
