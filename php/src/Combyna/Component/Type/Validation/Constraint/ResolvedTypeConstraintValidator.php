<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Validation\Constraint;

use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ResolvedTypeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResolvedTypeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ResolvedTypeConstraint::class => [$this, 'validate']
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
     * @param ResolvedTypeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ResolvedTypeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if ($constraint->getType() instanceof UnresolvedType) {
            $validationContext->addGenericViolation(
                'Expected type not to be unresolved, but it was: ' .
                $constraint->getType()->getSummary()
            );
        }
    }
}
