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

use Combyna\Component\Router\Validation\Query\CurrentViewRouteHasParameterQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class RouteParameterExistsConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteParameterExistsConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            RouteParameterExistsConstraint::class => [$this, 'validate']
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
     * @param RouteParameterExistsConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        RouteParameterExistsConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $routeDefinesParameter = $validationContext->queryForBoolean(
            new CurrentViewRouteHasParameterQuery($constraint->getParameterName()),
            $validationContext->getCurrentActNode()
        );

        if (!$routeDefinesParameter) {
            $validationContext->addGenericViolation(
                sprintf('Current view routes do not all define parameter "%s" or do not define it identically',
                $constraint->getParameterName()
            ));
        }
    }
}
