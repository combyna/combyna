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

use Combyna\Component\Ui\Validation\Query\PageViewExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class PageViewExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            PageViewExistsConstraint::class => [$this, 'validate']
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
     * @param PageViewExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        PageViewExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $pageViewExists = $validationContext->queryForBoolean(
            new PageViewExistsQuery($constraint->getPageViewName()),
            $validationContext->getActNode()
        );

        if (!$pageViewExists) {
            $validationContext->addGenericViolation(
                'Page view "' .
                $constraint->getPageViewName() .
                '" does not exist'
            );
        }
    }
}
