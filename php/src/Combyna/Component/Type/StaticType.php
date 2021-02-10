<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticListExpression;
use InvalidArgumentException;
use LogicException;

/**
 * Class StaticType
 *
 * Defines that an expression may resolve to a static value of a certain class,
 * eg. that an arithmetic expression may resolve to a `NumberExpression` static
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticType implements TypeInterface
{
    /**
     * @var string
     */
    private $staticClass;

    /**
     * @param string $staticClass
     */
    public function __construct($staticClass)
    {
        $this->staticClass = $staticClass;

        if (!is_a($staticClass, StaticInterface::class, true)) {
            // Only static expressions may be matched. Passing a non-static expression,
            // eg. a BinaryArithmeticExpression is invalid as an expression can never
            // evaluate to anything other than a static
            throw new InvalidArgumentException(
                'StaticType must be passed a static expression class'
            );
        }

        if (is_a($staticClass, StaticListExpression::class, true)) {
            // StaticListExpression is a type of static, but it _cannot_ be matched with a StaticType
            throw new InvalidArgumentException(
                'StaticListExpression must be matched with a StaticListType, not a StaticType'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        // Static type cannot allow all other types, only other static types
        // of the correct class (and not static lists, for example)
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsMultipleType(MultipleType $subType, array $subSubTypes)
    {
        foreach ($subSubTypes as $subSubType) {
            if (!$this->allows($subSubType)) {
                // One of the sub-types of the multiple one is not allowed by this super static type,
                // so we don't allow it
                return false;
            }
        }

        // All sub-types of this one are allowed by the super static type
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        return $static instanceof $this->staticClass;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return false; // A static list can never be a valid value for a non-list static
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return false; // A static structure can never be a valid value for a non-structure static
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        // Another static type will match this one if they simply match the same static expression class
        return $candidateType->staticClass === $this->staticClass;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsVoidType(VoidType $candidateType)
    {
        return true; // Void type can be passed anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        // Just check that the static is valid for this type
        if (!$this->allowsStatic($static)) {
            throw new LogicException(sprintf(
                'Expected a %s, got a %s',
                $this->staticClass,
                get_class($static)
            ));
        }

        return $static;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType()
    {
        return true; // "Any" type allows any other type
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $expressionClass = $this->staticClass;

        return $expressionClass::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithMultipleType(MultipleType $otherType, array $subSubTypes)
    {
        $combinedSubTypes = array_merge([$this], $subSubTypes);

        return new MultipleType($combinedSubTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType)
    {
        // There is nothing common to merge between a static list type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        // There is nothing common to merge between a static structure type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        if ($this->staticClass === $otherType->staticClass) {
            // Static types are equivalent, just return the existing one
            return $this;
        }

        // Otherwise return a MultipleType that will match everything this type does
        // and everything the other type does
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        // There is nothing common to merge between a static list type and an unresolved type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $unresolvedType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the static type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $otherType)
    {
        return $otherType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $otherType)
    {
        return $otherType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $otherType)
    {
        return $otherType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithStaticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithStaticType($this);
    }
}
