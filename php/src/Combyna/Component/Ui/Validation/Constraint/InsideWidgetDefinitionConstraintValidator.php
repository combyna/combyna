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

use Combyna\Component\Ui\Validation\Query\InsideWidgetDefinitionQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class InsideWidgetDefinitionConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideWidgetDefinitionConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            InsideWidgetDefinitionConstraint::class => [$this, 'validate']
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
     * @param InsideWidgetDefinitionConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        InsideWidgetDefinitionConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if (
            !$validationContext->queryForBoolean(
                new InsideWidgetDefinitionQuery(),
                $validationContext->getCurrentActNode()
            )
        ) {
            $validationContext->addGenericViolation('Not inside a widget definition');
        }
    }
}
