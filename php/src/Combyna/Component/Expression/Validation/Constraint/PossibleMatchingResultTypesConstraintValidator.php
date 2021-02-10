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

use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class PossibleMatchingResultTypesConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PossibleMatchingResultTypesConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            PossibleMatchingResultTypesConstraint::class => [$this, 'validate']
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
     * @param PossibleMatchingResultTypesConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        PossibleMatchingResultTypesConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $leftOperandCandidateResultType = $validationContext->getExpressionResultType(
            $constraint->getLeftOperandExpressionNode()
        );
        $rightOperandCandidateResultType = $validationContext->getExpressionResultType(
            $constraint->getRightOperandExpressionNode()
        );

        foreach ($constraint->getAllowedMatchingResultTypes() as $allowedMatchingResultType) {
            if (
                $allowedMatchingResultType->allows($leftOperandCandidateResultType) &&
                $allowedMatchingResultType->allows($rightOperandCandidateResultType)
            ) {
                // Both operands' expressions are allowed by this possible result type
                return;
            }
        }

        $validationContext->addGenericViolation(
            'operands "' . $leftOperandCandidateResultType->getSummary() .
            '" and "' . $rightOperandCandidateResultType->getSummary() .
            '" do not both match just one of the provided allowed result types, ' .
            'allowed types are: "' .
            implode(
                '", "',
                array_map(
                    function (TypeInterface $allowedMatchingResultType) {
                        return $allowedMatchingResultType->getSummary();
                    },
                    $constraint->getAllowedMatchingResultTypes()
                )
            )
            . '"'
        );
    }
}
