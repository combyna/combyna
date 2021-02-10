<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ExpressionComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.assurance_loader',
                'combyna.expression.loader.assurance',
                'addLoader'
            ),
            new DelegateeTagDefinition(
                'combyna.assurance_promoter',
                'combyna.expression.act.assurance_promoter',
                'addPromoter'
            ),
            new DelegateeTagDefinition(
                'combyna.builtin_expression_loader',
                'combyna.expression.loader.builtin',
                'addBuiltinLoader'
            ),
            new DelegateeTagDefinition(
                'combyna.expression_loader',
                'combyna.expression.loader',
                'addLoader'
            ),
            new DelegateeTagDefinition(
                'combyna.expression_promoter',
                'combyna.expression.act.promoter',
                'addPromoter'
            )
        ]));
    }
}
