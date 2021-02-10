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
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\Exception\IncompatibleStaticForCoercionException;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class VoidType
 *
 * Represents a type where no value will be provided.
 *
 * For example, if you tried to assign an empty list to a type of list<number>,
 * and an empty list was represented as list<any> (as we cannot derive a type
 * for the elements of an empty list) it wouldn't be allowed, as the `any`
 * element type could contain values other than numbers. To solve this, we create
 * a `void` element type, as that represents that we have no defined type to use.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VoidType implements TypeInterface
{
    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param string $contextDescription
     * @param ValidationContextInterface $validationContext
     */
    public function __construct($contextDescription, ValidationContextInterface $validationContext)
    {
        $this->contextDescription = $contextDescription;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsExoticType(ExoticType $candidateType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsMultipleType(MultipleType $candidateType, array $subSubTypes)
    {
        foreach ($subSubTypes as $subSubType) {
            if (!$this->allows($subSubType)) {
                // One of the sub-types of the multiple one is not allowed by this super type,
                // so we don't allow it
                return false;
            }
        }

        // All sub-types of the multiple one are allowed by this one
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        return false; // Void types only allow other void types
    }

    /**
     * {@inheritdoc}
     */
    public function allowsVoidType(VoidType $candidateType)
    {
        return true; // This will probably never be used, as void cannot be used as an "accepting" type
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
        throw new IncompatibleNativeForCoercionException(
            'Unable to coerce a native for void type: ' . $this->contextDescription
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        throw new IncompatibleStaticForCoercionException(
            'Tried to coerce a static for void type: ' . $this->contextDescription
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType(AnyType $superType)
    {
        // Void types cannot provide a value, so they cannot be allowed to be passed where a value is required,
        // even "any" type of value
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $otherType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $otherType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'void<' . $this->contextDescription . '>';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        return $this->getSummary(); // No value information to add
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
        return false; // No value information
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        // No type was known before, so there is nothing to add to the Any type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithExoticType(ExoticType $otherType)
    {
        // No type was known before, so there is nothing to add to the Exotic type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithMultipleType(MultipleType $otherType, array $subSubTypes)
    {
        // No type was known before, so there is nothing to add to the Multiple type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType)
    {
        // No type was known before, so there is nothing to add to the StaticList type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        // No type was known before, so there is nothing to add to the StaticStructure type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        // No type was known before, so there is nothing to add to the Static type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        // No type was known before, so there is nothing to add to the Unresolved type
        return $unresolvedType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        // No type was known before, so there is nothing to add to the Valued type
        return $otherType;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        // Keep both void types, as each could have different context descriptions
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $otherType)
    {
        return $otherType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $otherType)
    {
        return $otherType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $otherType)
    {
        return $otherType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithVoidType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithVoidType($this);
    }
}
