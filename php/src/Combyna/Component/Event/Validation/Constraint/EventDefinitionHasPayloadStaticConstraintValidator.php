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

use Combyna\Component\Event\Validation\Query\CurrentEventHasPayloadStaticQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class EventDefinitionHasPayloadStaticConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionHasPayloadStaticConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            EventDefinitionHasPayloadStaticConstraint::class => [$this, 'validate']
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
     * @param EventDefinitionHasPayloadStaticConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        EventDefinitionHasPayloadStaticConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $payloadStaticExists = $validationContext->queryForBoolean(
            new CurrentEventHasPayloadStaticQuery(
                $constraint->getPayloadStaticName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$payloadStaticExists) {
            $validationContext->addGenericViolation(
                'Payload does not contain a static with name "' .
                $constraint->getPayloadStaticName() .
                '"'
            );
        }
    }
}
