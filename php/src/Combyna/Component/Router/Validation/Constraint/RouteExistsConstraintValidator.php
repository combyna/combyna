<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Validation\Constraint;

use Combyna\Component\Router\Validation\Query\RouteExistsQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class RouteExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            RouteExistsConstraint::class => [$this, 'validate']
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
     * @param RouteExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        RouteExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $routeExists = $validationContext->queryForBoolean(
            new RouteExistsQuery(
                $constraint->getLibraryName(),
                $constraint->getRouteName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$routeExists) {
            $validationContext->addGenericViolation(sprintf(
                'Route "%s.%s" is not defined',
                $constraint->getLibraryName(),
                $constraint->getRouteName()
            ));
        }
    }
}
