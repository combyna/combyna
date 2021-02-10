<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Constraint;

use Combyna\Component\Signal\Validation\Query\SourceSignalHasPayloadStaticQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SourceSignalHasPayloadStaticConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SourceSignalHasPayloadStaticConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            SourceSignalHasPayloadStaticConstraint::class => [$this, 'validate']
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
     * @param SourceSignalHasPayloadStaticConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        SourceSignalHasPayloadStaticConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $payloadStaticExists = $validationContext->queryForBoolean(
            new SourceSignalHasPayloadStaticQuery(
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
