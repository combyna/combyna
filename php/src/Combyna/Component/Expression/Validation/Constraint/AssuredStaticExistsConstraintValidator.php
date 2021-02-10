<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

use Combyna\Component\Expression\Validation\Query\AssuredStaticExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AssuredStaticExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredStaticExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            AssuredStaticExistsConstraint::class => [$this, 'validate']
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
     * @param AssuredStaticExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        AssuredStaticExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $assuredStaticExists = $validationContext->queryForBoolean(
            new AssuredStaticExistsQuery($constraint->getStaticName()),
            $validationContext->getCurrentActNode()
        );

        if (!$assuredStaticExists) {
            $validationContext->addGenericViolation(
                'Assured static "' .
                $constraint->getStaticName() .
                '" is not defined'
            );
        }
    }
}
