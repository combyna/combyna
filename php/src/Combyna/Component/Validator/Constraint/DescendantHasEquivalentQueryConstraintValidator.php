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
 * Class DescendantHasEquivalentQueryConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DescendantHasEquivalentQueryConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            DescendantHasEquivalentQueryConstraint::class => [$this, 'validate']
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
     * @param DescendantHasEquivalentQueryConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        DescendantHasEquivalentQueryConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $querySpecifier = $constraint->getQuerySpecifier();

        $descendantSpecsWithQuery = $validationContext->getDescendantSpecsWithQuery($querySpecifier);

        if (count($descendantSpecsWithQuery) === 0) {
            $validationContext->addGenericViolation(
                'Could not find a descendant with an equivalent query: ' .
                $querySpecifier->getDescription()
            );
        }
    }
}
