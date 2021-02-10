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

use Combyna\Component\Environment\Config\Act\FunctionNodeInterface;
use Combyna\Component\Expression\Validation\Query\FunctionNodeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ValidFunctionCallConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidFunctionCallConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ValidFunctionCallConstraint::class => [$this, 'validate']
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
     * @param ValidFunctionCallConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ValidFunctionCallConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        // May return an UnknownFunctionNode or UnknownLibraryForFunctionNode if invalid -
        // it is expected that a FunctionExistsConstraint will be used in tandem
        $functionNode = $validationContext->queryForActNode(
            new FunctionNodeQuery(
                $constraint->getLibraryName(),
                $constraint->getFunctionName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$functionNode instanceof FunctionNodeInterface) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Expected a function node, got "%s"',
                    get_class($functionNode)
                )
            );

            return;
        }

        $functionNode->validateArgumentExpressionBag($validationContext, $constraint->getArgumentExpressionBag());
    }
}
