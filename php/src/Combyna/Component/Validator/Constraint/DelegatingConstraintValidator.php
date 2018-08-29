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

use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use InvalidArgumentException;

/**
 * Class DelegatingConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingConstraintValidator implements DelegatingConstraintValidatorInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $constraintValidatorCallablesByClass = [];

    /**
     * @var ConstraintValidatorInterface[]
     */
    private $constraintValidatorsByClass = [];

    /**
     * {@inheritdoc}
     */
    public function addConstraintValidator(ConstraintValidatorInterface $constraintValidator)
    {
        foreach (
            $constraintValidator->getConstraintClassToValidatorCallableMap() as
            $constraintClass => $validatorCallable
        ) {
            $this->constraintValidatorsByClass[$constraintClass] = $constraintValidator;
            $this->constraintValidatorCallablesByClass[$constraintClass] = $validatorCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpecPassesForConstraints(array $constraintClasses)
    {
        $passClassNames = [];

        foreach ($constraintClasses as $constraintClass) {
            if (!array_key_exists($constraintClass, $this->constraintValidatorsByClass)) {
                throw new InvalidArgumentException(sprintf(
                    'No validator is registered for constraint "%s"',
                    $constraintClass
                ));
            }

            $passClassNames = array_merge(
                $passClassNames,
                $this->constraintValidatorsByClass[$constraintClass]->getPassClasses()
            );
        }

        return array_unique($passClassNames);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(
        ConstraintInterface $constraint,
        ValidationContextInterface $validationContext
    ) {
        $constraintClass = get_class($constraint);

        if (!array_key_exists($constraintClass, $this->constraintValidatorCallablesByClass)) {
            throw new InvalidArgumentException(sprintf(
                'No validator is registered for constraint "%s"',
                $constraintClass
            ));
        }

        $constraintValidatorCallable = $this->constraintValidatorCallablesByClass[$constraintClass];

        $constraintValidatorCallable(
            $constraint,
            $validationContext
        );
    }
}
