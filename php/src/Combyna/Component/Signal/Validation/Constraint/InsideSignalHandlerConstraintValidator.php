<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Constraint;

use Combyna\Component\Signal\Validation\Query\InsideSignalHandlerQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class InsideSignalHandlerConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideSignalHandlerConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            InsideSignalHandlerConstraint::class => [$this, 'validate']
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
     * @param InsideSignalHandlerConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        InsideSignalHandlerConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if (
            !$validationContext->queryForBoolean(
                new InsideSignalHandlerQuery(),
                $validationContext->getActNode()
            )
        ) {
            $validationContext->addGenericViolation('Not inside a signal handler');
        }
    }
}
