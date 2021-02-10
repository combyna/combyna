<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RouterComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.exotic_type_determiner_factory',
                'combyna.type.exotic.exotic_type_determiner_factory',
                'addFactory'
            )
        ]));
    }
}
