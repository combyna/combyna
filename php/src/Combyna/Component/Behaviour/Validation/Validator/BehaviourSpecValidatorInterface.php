<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Validation\Validator;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface BehaviourSpecValidatorInterface
 *
 * Validates behaviour specifications
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecValidatorInterface
{
    /**
     * Validates a spec's ACT node when attached to the tree
     *
     * @param BehaviourSpecInterface $behaviourSpec
     * @param RootValidationContextInterface $rootValidationContext
     */
    public function validate(
        BehaviourSpecInterface $behaviourSpec,
        RootValidationContextInterface $rootValidationContext
    );

    /**
     * Validates a spec's ACT node when detached from the tree (eg. nodes created dynamically during validation)
     *
     * @param BehaviourSpecInterface $behaviourSpec
     * @param RootValidationContextInterface $rootValidationContext
     * @param SubValidationContextInterface $parentValidationContext
     */
    public function validateSpec(
        BehaviourSpecInterface $behaviourSpec,
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $parentValidationContext
    );
}
