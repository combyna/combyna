<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework;

use Combyna\Component\Common\ComponentInterface;
use Combyna\Component\Common\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Combyna\Component\Plugin\PluginInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Runtime
 *
 * Manages a Combyna component or platform
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Runtime
{
    /**
     * @var ComponentInterface[]
     */
    private $components;

    /**
     * @param ComponentInterface[] $components
     */
    public function __construct(array $components)
    {
        $this->components = $components;
    }

    /**
     * Boots the runtime, modifying the DI container as required
     *
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container)
    {
        // Set up delegators at this point, as they would otherwise set up circular dependencies
        // between the service definitions

        /** @var DelegatorInitialiser $delegatorInitialiser */
        $delegatorInitialiser = $container->get(DelegatorInitialiser::SERVICE_ID);

        $delegatorInitialiser->initialise();
    }

    /**
     * Modifies the DI container as required by the installed Combyna components
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function compile(ContainerBuilder $containerBuilder)
    {
        $componentExtensions = [];

        foreach ($this->components as $component) {
            $extension = $component->getContainerExtension();

            if ($extension !== null) {
                $containerBuilder->registerExtension($extension);

                $componentExtensions[] = $extension;
            }

            // Plugins need to load their library configs after the AutowirePass
            // so that the parser service(s) can be fetched
            if ($component instanceof PluginInterface) {
                $containerBuilder->addCompilerPass($component, PassConfig::TYPE_OPTIMIZE);
            }
        }

        // Ensure the extensions for components are always loaded
        $containerBuilder->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass(
                $containerBuilder->getCompilerPassConfig()->getMergePass(),
                $componentExtensions
            )
        );
    }
}
