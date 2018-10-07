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

use Combyna\Component\Ui\Validation\Query\WidgetHasValueQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class WidgetHasValueConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetHasValueConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            WidgetHasValueConstraint::class => [$this, 'validate']
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
     * @param WidgetHasValueConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        WidgetHasValueConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $valueExists = $validationContext->queryForBoolean(
            new WidgetHasValueQuery(
                $constraint->getValueName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$valueExists) {
            $validationContext->addGenericViolation(
                'Widget does not define a value with name "' .
                $constraint->getValueName() .
                '"'
            );
        }
    }
}
