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

use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class ResultTypeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResultTypeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     */
    public function __construct(ValidationFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ResultTypeConstraint::class => [$this, 'validate']
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
     * @param ResultTypeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ResultTypeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $candidateResultType = $validationContext->getExpressionResultType($constraint->getExpressionNode());

        if (!$constraint->getAllowedType()->allows($candidateResultType)) {
            $validationContext->addTypeMismatchViolation(
                $constraint->getAllowedType(),
                $candidateResultType,
                $constraint->getContextDescription()
            );
        }
    }
}
