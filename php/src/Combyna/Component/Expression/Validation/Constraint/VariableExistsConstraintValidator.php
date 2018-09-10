<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

use Combyna\Component\Expression\Validation\Query\VariableExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class VariableExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            VariableExistsConstraint::class => [$this, 'validate']
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
     * @param VariableExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        VariableExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $variableExists = $validationContext->queryForBoolean(
            new VariableExistsQuery($constraint->getVariableName()),
            $validationContext->getCurrentActNode()
        );

        if (!$variableExists) {
            $validationContext->addGenericViolation(
                'Variable "' .
                $constraint->getVariableName() .
                '" does not exist'
            );
        }
    }
}
