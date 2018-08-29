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

use Combyna\Component\Ui\Validation\Query\InsideCompoundWidgetDefinitionRootWidgetQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class InsideCompoundWidgetDefinitionRootWidgetConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InsideCompoundWidgetDefinitionRootWidgetConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            InsideCompoundWidgetDefinitionRootWidgetConstraint::class => [$this, 'validate']
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
     * @param InsideCompoundWidgetDefinitionRootWidgetConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        InsideCompoundWidgetDefinitionRootWidgetConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        if (
            !$validationContext->queryForBoolean(
                new InsideCompoundWidgetDefinitionRootWidgetQuery(),
                $validationContext->getActNode()
            )
        ) {
            $validationContext->addGenericViolation('Not inside a compound widget definition\'s root widget');
        }
    }
}
