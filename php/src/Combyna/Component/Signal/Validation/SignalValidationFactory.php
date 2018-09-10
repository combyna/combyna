<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Signal\Validation\Context\SignalHandlerSubValidationContext;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class SignalValidationFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalValidationFactory implements SignalValidationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSignalHandlerContext(
        SubValidationContextInterface $parentContext,
        SignalHandlerNode $signalHandlerNode,
        BehaviourSpecInterface $signalHandlerNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new SignalHandlerSubValidationContext(
            $parentContext,
            $signalHandlerNode,
            $signalHandlerNodeBehaviourSpec,
            $subjectNode
        );
    }
}
