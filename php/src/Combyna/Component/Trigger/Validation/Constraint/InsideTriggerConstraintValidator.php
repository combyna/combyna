<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Validation\Constraint;

use Combyna\Component\Trigger\Validation\Query\InsideTriggerQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class InsideTriggerConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideTriggerConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            InsideTriggerConstraint::class => [$this, 'validate']
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
     * @param InsideTriggerConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        InsideTriggerConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if (
            !$validationContext->queryForBoolean(
                new InsideTriggerQuery(),
                $validationContext->getCurrentActNode()
            )
        ) {
            $validationContext->addGenericViolation('Not inside a trigger');
        }
    }
}
