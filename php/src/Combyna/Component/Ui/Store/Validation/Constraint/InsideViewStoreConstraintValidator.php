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

use Combyna\Component\Ui\Store\Validation\Query\InsideViewStoreQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class InsideViewStoreConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideViewStoreConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            InsideViewStoreConstraint::class => [$this, 'validate']
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
     * @param InsideViewStoreConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        InsideViewStoreConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if (
            !$validationContext->queryForBoolean(
                new InsideViewStoreQuery(),
                $validationContext->getActNode()
            )
        ) {
            $validationContext->addGenericViolation('Not inside a view store');
        }
    }
}
