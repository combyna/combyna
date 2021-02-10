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
use Combyna\Component\App\Validation\Context\AppSubValidationContext;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class AppValidationFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppValidationFactory implements AppValidationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAppContext(
        SubValidationContextInterface $parentContext,
        AppNode $appNode,
        BehaviourSpecInterface $appNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new AppSubValidationContext($parentContext, $appNode, $appNodeBehaviourSpec, $subjectNode);
    }
}
