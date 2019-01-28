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
use Combyna\Component\Environment\DependencyInjection\RegisterNativeFunctionProvidersPass;
use Combyna\Component\Environment\DependencyInjection\RegisterWidgetValueProvidersPass;
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
        // Register the native PHP implementation for native functions
        $containerBuilder->addCompilerPass(new RegisterNativeFunctionProvidersPass());

        // Register the providers for widget values
        $containerBuilder->addCompilerPass(new RegisterWidgetValueProvidersPass());
    }
}
