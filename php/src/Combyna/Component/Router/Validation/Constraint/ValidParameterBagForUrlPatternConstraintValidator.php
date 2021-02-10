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

use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ValidParameterBagForUrlPatternConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidParameterBagForUrlPatternConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ValidParameterBagForUrlPatternConstraint::class => [$this, 'validate']
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
     * @param ValidParameterBagForUrlPatternConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ValidParameterBagForUrlPatternConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $patternParameterNames = $this->getParameterNamesForUrlPattern($constraint->getUrlPattern());
        $bagParameterNames = $constraint->getParameterBagModel()->getStaticDefinitionNames();

        $patternParametersMissingFromBag = array_diff($patternParameterNames, $bagParameterNames);
        $unnecessaryBagParameters = array_diff($bagParameterNames, $patternParameterNames);

        if (count($patternParametersMissingFromBag) > 0) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Some URL parameter placeholder(s) are missing definitions: "%s"',
                    implode('", "', $patternParametersMissingFromBag)
                )
            );
        }

        if (count($unnecessaryBagParameters) > 0) {
            $validationContext->addGenericViolation(
                sprintf(
                    'Unnecessary URL parameter definition(s): "%s"',
                    implode('", "', $unnecessaryBagParameters)
                )
            );
        }
    }

    /**
     * Fetches all parameter names from placeholders in the URL pattern
     *
     * @param string $urlPattern
     * @return string[]
     */
    private function getParameterNamesForUrlPattern($urlPattern)
    {
        $patternParameterNames = [];

        preg_match_all('/\{([^}]+)\}/', $urlPattern, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $patternParameterNames[] = $match[1];
        }

        return $patternParameterNames;
    }
}
