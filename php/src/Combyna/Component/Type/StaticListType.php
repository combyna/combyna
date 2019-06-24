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
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\Exception\IncompatibleStaticForCoercionException;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StaticListType
 *
 * Matches an expression whose eventual result static is a static list with a certain result type for its elements
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListType implements TypeInterface
{
    /**
     * @var TypeInterface
     */
    private $elementType;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param TypeInterface $elementType Type allowed for elements of the list
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(TypeInterface $elementType, ValidationContextInterface $validationContext)
    {
        $this->elementType = $elementType;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByStaticListType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return false; // A static list can only match specific other types, not "any" type
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
                // by this super static list type, so we don't allow
                return false;
            }
        }

        // All sub-types of the multiple one are allowed by this super static list type
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        if (!$static instanceof StaticListExpression) {
            return false;
        }

        return $static->elementsMatch($this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        // As long as the candidate sub-static-list type's element type is allowed
        // by our own element type, then let it through. If the source list was empty,
        // its element type would be the special Void type, which will be allowed
        // for any target list element type. This is to allow a list with any possible element type
        // to be passed an empty list.
        return $this->elementType->allows($elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return false; // We require a list, which a structure type can never be
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return false; // We require a list, which a non-list static type can never be
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        // Discard the value of the valued type and just check whether
        // this list type allows the value's wrapped type through
        return $this->allows($candidateType->getWrappedType());
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
        if ($nativeValue instanceof StaticInterface) {
            if (!$this->allowsStatic($nativeValue)) {
                throw new IncompatibleNativeForCoercionException(
                    sprintf(
                        'Static of type "%s" was given, expected a native or matching static of type "%s"',
                        $nativeValue->getType(),
                        $this->getSummary()
                    )
                );
            }

            // Already a static, but we still need to perform static coercion
            // so that any incomplete statics may be completed
            return $this->coerceStatic($nativeValue, $evaluationContext);
        }

        if (!is_array($nativeValue)) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Static list type expects an array, %s given',
                gettype($nativeValue)
            ));
        }

        return $staticExpressionFactory->createStaticListExpression(
            $bagFactory->createStaticList(
                array_map(
                    function ($elementValue) use (
                        $bagFactory,
                        $evaluationContext,
                        $staticExpressionFactory
                    ) {
                        return $this->elementType->coerceNative(
                            $elementValue,
                            $staticExpressionFactory,
                            $bagFactory,
                            $evaluationContext
                        );
                    },
                    $nativeValue
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        if (!$static instanceof StaticListExpression) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Expected a %s, got %s',
                StaticListExpression::class,
                get_class($static)
            ));
        }

        $coercedElementStatics = [];
        $coercionWasNeeded = true;

        foreach ($static->getElementStatics() as $index => $elementStatic) {
            $coercedElementStatic = $this->elementType->coerceStatic($elementStatic, $evaluationContext);

            if ($coercedElementStatic === $elementStatic) {
                $coercedElementStatics[] = $elementStatic;
            } else {
                $coercedElementStatics[] = $coercedElementStatic;
                $coercionWasNeeded = true;
            }
        }

        if (!$coercionWasNeeded) {
            return $static; // Just return the original list if none of its elements needed coercion
        }

        return $static->withElements($coercedElementStatics);
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
    public function isAllowedByAnyType(AnyType $superType)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $otherType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $otherType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsStaticListType($this, $this->elementType);
    }

    /**
     * Fetches the type for elements of lists of this type
     *
     * @return TypeInterface
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'list<' . $this->elementType->getSummary() . '>';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        return 'list<' . $this->elementType->getSummaryWithValue() . '>';
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        // The list itself never stores any value information, but its element type may
        return $this->elementType->hasValue();
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithStaticListType($this);
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
        // To merge two static list types together, create a new static list type
        // whose element type encompasses this one's element type and the other type's element type
        $combinedElementType = $this->elementType->mergeWith($elementType);

        return new StaticListType($combinedElementType, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        // There is nothing common to merge between a static list type and a static structure type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        // There is nothing common to merge between a static list type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        // There is nothing common to merge between a static list type and an unresolved type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $unresolvedType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        // There is nothing common to merge between a static list type and a valued type,
        // so just return a MultipleType that allows both
        // TODO: Consider merging this type with the valued type's wrapped type
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the static list type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $otherType)
    {
        return $otherType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $otherType)
    {
        return $otherType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $otherType)
    {
        return $otherType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithStaticListType($this, $this->elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithStaticListType($this, $this->elementType);
    }
}
