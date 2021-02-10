<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Constraint;

use Combyna\Component\Store\Config\Act\QueryNodeInterface;
use Combyna\Component\Ui\Store\Validation\Query\QueryNodeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class ValidViewStoreQueryConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidViewStoreQueryConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ValidViewStoreQueryConstraint::class => [$this, 'validate']
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
     * @param ValidViewStoreQueryConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ValidViewStoreQueryConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        // May return an UnknownQueryNode if invalid -
        // it is expected that a QueryExistsConstraint will be used in tandem
        $queryNode = $validationContext->queryForActNode(
            new QueryNodeQuery(
                $constraint->getQueryName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$queryNode instanceof QueryNodeInterface) {
            throw new LogicException(sprintf('Expected a query node, got "%s"', get_class($queryNode)));
        }

        $queryNode->validateArgumentExpressionBag($validationContext, $constraint->getArgumentExpressionBag());
    }
}
