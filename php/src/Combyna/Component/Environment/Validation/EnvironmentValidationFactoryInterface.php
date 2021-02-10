<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Validation;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Validation\Context\EnvironmentSubValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface EnvironmentValidationFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EnvironmentValidationFactoryInterface
{
    /**
     * Creates a EnvironmentSubValidationContext
     *
     * @param SubValidationContextInterface $parentContext
     * @param EnvironmentNode $environmentNode
     * @param BehaviourSpecInterface $environmentNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @return EnvironmentSubValidationContextInterface
     */
    public function createEnvironmentContext(
        SubValidationContextInterface $parentContext,
        EnvironmentNode $environmentNode,
        BehaviourSpecInterface $environmentNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    );
}
