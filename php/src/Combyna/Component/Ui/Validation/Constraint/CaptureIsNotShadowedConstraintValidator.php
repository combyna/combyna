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

use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CaptureIsNotShadowedConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsNotShadowedConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CaptureIsNotShadowedConstraint::class => [$this, 'validate']
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
     * @param CaptureIsNotShadowedConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CaptureIsNotShadowedConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $ancestorDefinesCapture = $validationContext->queryForBoolean(
            CaptureIsDefinedQuery::createIntendingToSet($constraint->getCaptureName()),
            $validationContext->getCurrentParentActNode()
        );

        if ($ancestorDefinesCapture) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Capture "%s" would shadow a capture of the same name that is defined by an ancestor',
                    $constraint->getCaptureName()
                )
            );
        }
    }
}
