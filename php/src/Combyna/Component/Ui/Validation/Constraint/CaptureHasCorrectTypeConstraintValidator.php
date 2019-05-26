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

use Combyna\Component\Ui\Validation\Query\CaptureTypeQuery;
use Combyna\Component\Ui\Validation\Query\CorrectCaptureTypeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CaptureHasCorrectTypeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureHasCorrectTypeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CaptureHasCorrectTypeConstraint::class => [$this, 'validate']
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
     * @param CaptureHasCorrectTypeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CaptureHasCorrectTypeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $definedCaptureType = $validationContext->queryForResultType(
            new CaptureTypeQuery($constraint->getCaptureName()),
            $validationContext->getCurrentActNode()
        );
        $setterExpressionResultType = $validationContext->queryForResultType(
            new CorrectCaptureTypeQuery($constraint->getCaptureName(), $constraint->getSetterExpressionNode()),
            $validationContext->getCurrentActNode()
        );

        if (!$definedCaptureType->allows($setterExpressionResultType)) {
            $validationContext->addTypeMismatchViolation(
                $definedCaptureType,
                $setterExpressionResultType,
                sprintf('Capture "%s"', $constraint->getCaptureName())
            );
        }
    }
}
