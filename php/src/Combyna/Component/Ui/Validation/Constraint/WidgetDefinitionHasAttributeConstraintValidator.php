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

use Combyna\Component\Ui\Validation\Query\CurrentWidgetDefinitionHasAttributeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class WidgetDefinitionHasAttributeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionHasAttributeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            WidgetDefinitionHasAttributeConstraint::class => [$this, 'validate']
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
     * @param WidgetDefinitionHasAttributeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        WidgetDefinitionHasAttributeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $attributeExists = $validationContext->queryForBoolean(
            new CurrentWidgetDefinitionHasAttributeQuery(
                $constraint->getAttributeName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$attributeExists) {
            $validationContext->addGenericViolation(
                'Compound widget definition does not define an attribute with name "' .
                $constraint->getAttributeName() .
                '"'
            );
        }
    }
}
