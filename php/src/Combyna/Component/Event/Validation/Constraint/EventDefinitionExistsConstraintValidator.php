<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Validation\Constraint;

use Combyna\Component\Event\Validation\Query\EventDefinitionExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class EventDefinitionExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            EventDefinitionExistsConstraint::class => [$this, 'validate']
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
     * @param EventDefinitionExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        EventDefinitionExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $eventDefinitionExists = $validationContext->queryForBoolean(
            new EventDefinitionExistsQuery(
                $constraint->getLibraryName(),
                $constraint->getEventName()
            ),
            $validationContext->getActNode()
        );

        if (!$eventDefinitionExists) {
            $validationContext->addGenericViolation(sprintf(
                'Event "%s.%s" is not defined',
                $constraint->getLibraryName(),
                $constraint->getEventName()
            ));
        }
    }
}
