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
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Signal\Validation\Context\SignalHandlerSubValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface SignalValidationFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalValidationFactoryInterface
{
    /**
     * Creates a SignalHandlerSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param SignalHandlerNode $signalHandlerNode
     * @param BehaviourSpecInterface $signalHandlerNodeBehaviourSpec
     * @return SignalHandlerSubValidationContextInterface
     */
    public function createSignalHandlerContext(
        SubValidationContextInterface $parentContext,
        SignalHandlerNode $signalHandlerNode,
        BehaviourSpecInterface $signalHandlerNodeBehaviourSpec
    );
}
