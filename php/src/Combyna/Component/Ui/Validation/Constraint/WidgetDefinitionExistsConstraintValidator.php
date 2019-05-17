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

use Combyna\Component\Ui\Validation\Query\WidgetDefinitionExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class WidgetDefinitionExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            WidgetDefinitionExistsConstraint::class => [$this, 'validate']
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
     * @param WidgetDefinitionExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        WidgetDefinitionExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $widgetDefinitionExists = $validationContext->queryForBoolean(
            new WidgetDefinitionExistsQuery(
                $constraint->getLibraryName(),
                $constraint->getWidgetDefinitionName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$widgetDefinitionExists) {
            $validationContext->addGenericViolation(sprintf(
                'Widget definition "%s.%s" is not defined',
                $constraint->getLibraryName(),
                $constraint->getWidgetDefinitionName()
            ));
        }
    }
}
