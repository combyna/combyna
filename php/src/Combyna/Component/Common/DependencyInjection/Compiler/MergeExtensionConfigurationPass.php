<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass as BaseMergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class MergeExtensionConfigurationPass
 *
 * Ensures that extensions for Combyna components are always loaded. Symfony container extensions
 * are only loaded if they have configuration specified, by default.
 * 
 * Based on Symfony's HttpKernel MergeExtensionConfigurationPass class
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MergeExtensionConfigurationPass extends BaseMergeExtensionConfigurationPass
{
    /**
     * @var ExtensionInterface[]
     */
    private $extensions;

    /**
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($this->extensions as $extension) {
            if (!count($container->getExtensionConfig($extension->getAlias()))) {
                $container->loadFromExtension($extension->getAlias(), []);
            }
        }

        parent::process($container);
    }
}
