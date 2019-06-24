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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Type\Exception\IncompatibleStaticForCoercionException;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class ValuedType
 *
 * Captures a static value within the type system, to allow for static analysis
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValuedType implements TypeInterface
{
    /**
     * @var StaticValueInterface
     */
    private $staticValue;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @var TypeInterface
     */
    private $wrappedType;

    /**
     * @param TypeInterface $wrappedType
     * @param StaticValueInterface $staticValue
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        TypeInterface $wrappedType,
        StaticValueInterface $staticValue,
        ValidationContextInterface $validationContext
    ) {
        $this->staticValue = $staticValue;
        $this->validationContext = $validationContext;
        $this->wrappedType = $wrappedType;

        // TODO: Validate that the static value expression's result type
        //       matches the wrapped type. This is currently handled indirectly
        //       because both are checked in ::allowsValuedType(...), but it would
        //       be best to have an explicit check that they match,
        //       once types are refactored into *Type and *TypeNode classes.
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return false; // A valued type can only match specific other types, not "any" type
    }

    /**
     * {@inheritdoc}
     */
    public function allowsExoticType(ExoticType $candidateType)
    {
        // We cannot determine whether an exotic type should be allowed,
        // so instead we rely on the exotic determiner adding a violation
        // to fail validation if there is an issue
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsMultipleType(MultipleType $candidateType, array $subSubTypes)
    {
        foreach ($subSubTypes as $subSubType) {
            if (!$this->allows($subSubType)) {
                // One of the sub-types of the multiple one is not allowed
                // by this super valued type, so we don't allow
                return false;
            }
        }

        // All sub-types of the multiple one are allowed by this super valued type
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        // Ensure the static is of the same type as this valued type's
        if (!$static instanceof $this->staticValue) {
            return false;
        }

        return $static->equals($this->staticValue);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return false;  // We require a matching valued type
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return false;  // We require a matching valued type
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return false;  // We require a matching valued type
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        return $this->wrappedType->allows($candidateType->wrappedType) &&
            $candidateType->getStaticValue()->equals($this->staticValue);
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
    public function coerceNative(
        $nativeValue,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $evaluationContext
    ) {
        $coercedStatic = $this->wrappedType->coerceNative(
            $nativeValue,
            $staticExpressionFactory,
            $bagFactory,
            $evaluationContext
        );

        if (!$coercedStatic->equals($this->staticValue)) {
            throw new LogicException('Valued type can only accept an exact matching value');
        }

        return $coercedStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        if (!$static->equals($this->staticValue)) {
            throw new IncompatibleStaticForCoercionException('Valued type can only accept an exact matching value');
        }

        // No coercion necessary
        return $static;
    }

    /**
     * Fetches the static value for this type
     *
     * @return StaticValueInterface
     */
    public function getStaticValue()
    {
        return $this->staticValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        // For the usual summary, only return the wrapped type as the value information won't be useful
        return $this->wrappedType->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        return sprintf(
            '%s<%s>',
            $this->wrappedType->getSummary(),
            $this->staticValue->getSummary()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationContext()
    {
        return $this->validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        return true; // Valued types always store a value
    }

    /**
     * Fetches the wrapped type
     *
     * @return TypeInterface
     */
    public function getWrappedType()
    {
        return $this->wrappedType;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType(AnyType $superType)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $otherType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $otherType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithExoticType(ExoticType $otherType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithMultipleType(MultipleType $otherType, array $subSubTypes)
    {
        $combinedSubTypes = array_merge([$this], $subSubTypes);

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        return new MultipleType([$this, $unresolvedType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the valued type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithValuedType($this);
    }
}
