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

use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Validation\Query\AssuranceNodeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AssuredConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            AssuredConstraint::class => [$this, 'validate']
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
     * @param AssuredConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        AssuredConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $expressionNode = $constraint->getExpressionNode();

        if (!$expressionNode instanceof AssuredExpressionNode) {
            $validationContext->addGenericViolation(
                $constraint->getContextDescription() . ' expects "assured", got "' . $expressionNode->getType() . '"'
            );

            return;
        }

        $assuranceNode = $validationContext->queryForActNode(
            new AssuranceNodeQuery($expressionNode->getAssuredStaticName()),
            $validationContext->getCurrentActNode()
        );

        if (!$assuranceNode instanceof AssuranceNodeInterface) {
            $validationContext->addGenericViolation(sprintf(
                'Expected an assurance node, got "%s"',
                get_class($assuranceNode)
            ));

            return;
        }

        $expectedAssuranceConstraint = $constraint->getConstraint();
        $actualAssuranceConstraint = $assuranceNode->getConstraint();

        if ($actualAssuranceConstraint !== $expectedAssuranceConstraint) {
            $validationContext->addGenericViolation(
                $constraint->getContextDescription() . ' expects "' .
                $expectedAssuranceConstraint .
                '" constraint, got "' . $actualAssuranceConstraint . '"'
            );
        }
    }
}
