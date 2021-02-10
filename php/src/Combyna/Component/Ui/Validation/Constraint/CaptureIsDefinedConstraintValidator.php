<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CaptureIsDefinedConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsDefinedConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CaptureIsDefinedConstraint::class => [$this, 'validate']
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
     * @param CaptureIsDefinedConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CaptureIsDefinedConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $captureIsDefined = $validationContext->queryForBoolean(
            $constraint->createQuery(),
            $validationContext->getCurrentActNode()
        );

        if (!$captureIsDefined) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Capture "%s" is not defined',
                    $constraint->getCaptureName()
                )
            );
        }
    }
}
