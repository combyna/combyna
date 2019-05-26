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

use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StructureHasAttributeConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureHasAttributeConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            StructureHasAttributeConstraint::class => [$this, 'validate']
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
     * @param StructureHasAttributeConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        StructureHasAttributeConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        $structureType = $validationContext->getExpressionResultType($constraint->getStructureExpression());

        if ($structureType instanceof ValuedType) {
            $structureType = $structureType->getWrappedType();
        }

        if ($structureType instanceof StaticStructureType) {
            if (!$structureType->hasAttribute($constraint->getAttributeName())) {
                $validationContext->addGenericViolation(sprintf(
                    'Structure does not define an attribute with name "%s"',
                    $constraint->getAttributeName()
                ));
            }
        } else {
            $validationContext->addGenericViolation(sprintf(
                'Structure expression should result in a structure, but results in a "%s" instead',
                $structureType->getSummary()
            ));
        }
    }
}
