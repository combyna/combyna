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
 * Class CallbackConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CallbackConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CallbackConstraint::class => [$this, 'validate']
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
     * @param CallbackConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CallbackConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $callback = $constraint->getCallback();

        $callback($validationContext);
    }
}
