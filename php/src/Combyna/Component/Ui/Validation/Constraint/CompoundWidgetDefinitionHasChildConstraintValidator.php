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

use Combyna\Component\Ui\Validation\Query\CurrentCompoundWidgetDefinitionHasChildStaticQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CompoundWidgetDefinitionHasChildConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionHasChildConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            CompoundWidgetDefinitionHasChildConstraint::class => [$this, 'validate']
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
     * @param CompoundWidgetDefinitionHasChildConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        CompoundWidgetDefinitionHasChildConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $childExists = $validationContext->queryForBoolean(
            new CurrentCompoundWidgetDefinitionHasChildStaticQuery(
                $constraint->getChildName()
            ),
            $validationContext->getActNode()
        );

        if (!$childExists) {
            $validationContext->addGenericViolation(
                'Compound widget definition does not define a child with name "' .
                $constraint->getChildName() .
                '"'
            );
        }
    }
}
