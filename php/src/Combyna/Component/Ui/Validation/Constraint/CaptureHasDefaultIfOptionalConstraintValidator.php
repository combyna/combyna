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

use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNodeInterface;
use Combyna\Component\Ui\Validation\Query\CaptureDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\CaptureHasOptionalAncestorWidgetQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class CaptureHasDefaultIfOptionalConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureHasDefaultIfOptionalConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CaptureHasDefaultIfOptionalConstraint::class => [$this, 'validate']
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
     * @param CaptureHasDefaultIfOptionalConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CaptureHasDefaultIfOptionalConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        /*
         * Determine whether there is a conditional or repeater between where the capture is defined
         * and where it is set. This query will go up the tree until it either:
         *  - finds a conditional or repeater, returning true (and then not climbing any further), or
         *  - reaches the widget that defines the capture,
         *    returning false as no conditional or repeater must have been hit
         */
        $hasOptionalAncestorWidget = $validationContext->queryForBoolean(
            new CaptureHasOptionalAncestorWidgetQuery($constraint->getCaptureName()),
            $validationContext->getCurrentActNode()
        );

        if (!$hasOptionalAncestorWidget) {
            // Capture is set unconditionally - nothing further to do
            return;
        }

        $captureDefinitionNode = $validationContext->queryForActNode(
            new CaptureDefinitionNodeQuery($constraint->getCaptureName()),
            $validationContext->getCurrentActNode()
        );

        if (!$captureDefinitionNode instanceof FixedStaticDefinitionNodeInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    FixedStaticDefinitionNodeInterface::class,
                    get_class($captureDefinitionNode)
                )
            );
        }

        /*
         * At this point, we know the capture is only set optionally, because there is
         * a conditional or repeater between where it is defined and where it is set.
         * This means we need to ensure it has a default expression defined, which will be used
         * to determine its value in the scenario where its setter widget is not present.
         */

        if ($captureDefinitionNode->isRequired()) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Capture "%s" is only set optionally as it is inside a conditional or repeater, ' .
                    'but is missing a default expression',
                    $constraint->getCaptureName()
                )
            );
        }
    }
}
