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
use Combyna\Component\Environment\Validation\Context\EnvironmentSubValidationContext;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class EnvironmentValidationFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentValidationFactory implements EnvironmentValidationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createEnvironmentContext(
        SubValidationContextInterface $parentContext,
        EnvironmentNode $environmentNode,
        BehaviourSpecInterface $environmentNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        return new EnvironmentSubValidationContext(
            $parentContext,
            $environmentNode,
            $environmentNodeBehaviourSpec,
            $subjectNode
        );
    }
}
