<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Constraint;

use Combyna\Component\Ui\Store\Validation\Query\ViewStoreHasSlotQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewStoreHasSlotConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreHasSlotConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ViewStoreHasSlotConstraint::class => [$this, 'validate']
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
     * @param ViewStoreHasSlotConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ViewStoreHasSlotConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $slotExists = $validationContext->queryForBoolean(
            new ViewStoreHasSlotQuery(
                $constraint->getSlotName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$slotExists) {
            $validationContext->addGenericViolation(
                'View store does not contain a slot with name "' .
                $constraint->getSlotName() .
                '"'
            );
        }
    }
}
