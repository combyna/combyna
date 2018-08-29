<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Validation;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Validation\Context\AppSubValidationContextInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface AppValidationFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppValidationFactoryInterface
{
    /**
     * Creates an AppSubValidationContext
     *
     * @param SubValidationContextInterface $environmentParentContext
     * @param AppNode $appNode
     * @param BehaviourSpecInterface $appNodeBehaviourSpec
     * @return AppSubValidationContextInterface
     */
    public function createAppContext(
        SubValidationContextInterface $environmentParentContext,
        AppNode $appNode,
        BehaviourSpecInterface $appNodeBehaviourSpec
    );
}
