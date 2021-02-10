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

use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ValidWidgetValueProvidersConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidWidgetValueProvidersConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ValidWidgetValueProvidersConstraint::class => [$this, 'validate']
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
     * @param ValidWidgetValueProvidersConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ValidWidgetValueProvidersConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $valueNames = $constraint->getValueNames();
        $providedValueNames = array_keys($constraint->getValueNameToProviderCallableMap());
        $valueNamesMissingProviders = array_diff($valueNames, $providedValueNames);
        $unnecessaryProviderValueNames = array_diff($providedValueNames, $valueNames);

        if (count($valueNamesMissingProviders) > 0) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Some value(s) are missing providers: "%s"',
                    implode('", "', $valueNamesMissingProviders)
                )
            );
        }

        if (count($unnecessaryProviderValueNames) > 0) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Unnecessary value provider(s): "%s"',
                    implode('", "', $unnecessaryProviderValueNames)
                )
            );
        }
    }
}
