<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EnvironmentComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            // Register the native PHP implementation for native functions
            new DelegateeTagDefinition(
                'combyna.native_library_function_provider',
                'combyna.environment.event_listener.native_function_installer',
                'addProvider'
            ),

            // Register the providers for widget values
            new DelegateeTagDefinition(
                'combyna.widget_value_provider',
                'combyna.environment.event_listener.widget_value_provider_installer',
                'addProvider'
            )
        ]));
    }
}
