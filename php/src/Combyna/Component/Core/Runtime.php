<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Core;

use Combyna\Component\Common\ComponentInterface;
use Combyna\Component\Common\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
     */
    public function boot()
    {
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
        }

        // Ensure the extensions for components are always loaded
        $containerBuilder->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass($componentExtensions)
        );
    }
}
