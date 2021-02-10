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

use Combyna\Component\Signal\Validation\Query\SignalDefinitionExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SignalDefinitionExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            SignalDefinitionExistsConstraint::class => [$this, 'validate']
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
     * @param SignalDefinitionExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        SignalDefinitionExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $signalDefinitionExists = $validationContext->queryForBoolean(
            new SignalDefinitionExistsQuery(
                $constraint->getLibraryName(),
                $constraint->getSignalName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$signalDefinitionExists) {
            $validationContext->addGenericViolation(sprintf(
                'Signal "%s.%s" is not defined',
                $constraint->getLibraryName(),
                $constraint->getSignalName()
            ));
        }
    }
}
