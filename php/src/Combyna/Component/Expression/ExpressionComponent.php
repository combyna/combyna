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
use Combyna\Component\Expression\DependencyInjection\Compiler\RegisterAssuranceLoadersPass;
use Combyna\Component\Expression\DependencyInjection\Compiler\RegisterAssuranceNodePromotersPass;
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
        $containerBuilder->addCompilerPass(new RegisterAssuranceLoadersPass());
        $containerBuilder->addCompilerPass(new RegisterAssuranceNodePromotersPass());

        // TODO: Factor the other delegator setups out of ExpressionExtension
    }
}
