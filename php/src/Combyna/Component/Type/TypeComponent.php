<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TypeComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.type_loader',
                'combyna.type.loader',
                'addLoader'
            ),
            new DelegateeTagDefinition(
                'combyna.type_promoter',
                'combyna.type.promoter',
                'addPromoter'
            )
        ]));
    }
}
