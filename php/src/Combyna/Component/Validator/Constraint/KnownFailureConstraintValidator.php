<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Constraint;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class KnownFailureConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownFailureConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            KnownFailureConstraint::class => [$this, 'validate']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassClasses()
    {
        return []; // No passes needed
    }

    /**
     * Validates this constraint in the given validation context. If the constraint is not met,
     * one or more violations will be added to the context to make the validation fail
     *
     * @param KnownFailureConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        KnownFailureConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $validationContext->addGenericViolation($constraint->getFailureDescription());
    }
}
