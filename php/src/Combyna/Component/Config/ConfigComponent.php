<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ConfigComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.config_node_visitor',
                'combyna.config.node_visitor',
                'addVisitor'
            ),
            new DelegateeTagDefinition(
                'combyna.parameter_parser',
                'combyna.config.parameter.parser',
                'addParser'
            ),
            new DelegateeTagDefinition(
                'combyna.parameter_type_parser',
                'combyna.config.parameter.type_parser',
                'addParser'
            )
        ]));
    }
}
