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

use Combyna\Component\Ui\Behaviour\Query\Specifier\CaptureIsDefinedQuerySpecifier;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CaptureIsSetExactlyOnceConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsSetExactlyOnceConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CaptureIsSetExactlyOnceConstraint::class => [$this, 'validate']
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
     * @param CaptureIsSetExactlyOnceConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CaptureIsSetExactlyOnceConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        // Find the number of times a descendant widget sets the capture's value
        $specsThatSetCapture = $validationContext->getDescendantSpecsWithQuery(
            CaptureIsDefinedQuerySpecifier::createIntendingToSet($constraint->getCaptureName())
        );

        if (count($specsThatSetCapture) !== 1) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Capture "%s" should be set exactly once, but was set %d time(s)',
                    $constraint->getCaptureName(),
                    count($specsThatSetCapture)
                )
            );
        }
    }
}
