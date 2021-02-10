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
 * Class ResolvableTypeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResolvableTypeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ResolvableTypeConstraint::class => [$this, 'validate']
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
     * @param ResolvableTypeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ResolvableTypeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $type = $constraint->getTypeDeterminer()->determine($validationContext);

        if ($type instanceof UnresolvedType) {
            $validationContext->addGenericViolation(
                'Expected type not to be unresolved, but it was: ' .
                $type->getSummary()
            );
        }
    }
}
