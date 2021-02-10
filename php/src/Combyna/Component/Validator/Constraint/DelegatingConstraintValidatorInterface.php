<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Constraint;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface DelegatingConstraintValidatorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DelegatingConstraintValidatorInterface
{
    /**
     * Registers a validator for a type of constraint
     *
     * @param ConstraintValidatorInterface $constraintValidator
     */
    public function addConstraintValidator(ConstraintValidatorInterface $constraintValidator);

    /**
     * Fetches the classes of all behaviour spec passes needed by the provided constraints
     *
     * @param string[] $constraintClasses
     * @return string[]
     */
    public function getBehaviourSpecPassesForConstraints(array $constraintClasses);

    /**
     * Validates a constraint, populating a ValidationContext with any violations
     *
     * @param ConstraintInterface $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ConstraintInterface $constraint,
        ValidationContextInterface $validationContext
    );
}
