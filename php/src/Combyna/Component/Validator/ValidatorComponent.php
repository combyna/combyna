<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Combyna\Component\Common\DependencyInjection\Compiler\RegisterDelegateesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ValidatorComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidatorComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterDelegateesPass([
            new DelegateeTagDefinition(
                'combyna.validation_constraint_validator',
                'combyna.validator.delegating_constraint_validator',
                'addConstraintValidator'
            ),
            new DelegateeTagDefinition(
                'combyna.sub_validation_context_factory',
                'combyna.validator.delegating_sub_validation_context_factory',
                'addFactory'
            )
        ]));
    }
}
