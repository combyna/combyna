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
use Combyna\Component\Validator\Context\ValidationContextInterface;
use InvalidArgumentException;

/**
 * Class AnyType
 *
 * Matches any return value static type of an expression except an UnknownType
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AnyType implements TypeInterface
{
    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(ValidationContextInterface $validationContext)
    {
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return true;
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
        return true; // Any multiple type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        return true; // Any static type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return true; // Any static list type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return true; // Any static structure type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return true; // Any static type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        return true; // Any valued type is allowed
    }

    /**
     * {@inheritdoc}
     */
    public function allowsVoidType(VoidType $candidateType)
    {
        return true; // Void type can be passed anywhere, including to a special Any type
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
        // For an AnyType, the value could be anything, so we make a best guess based on its data

        if ($nativeValue instanceof StaticInterface) {
            // Already a static - nothing to do
            return $nativeValue;
        }

        if (is_bool($nativeValue)) {
            return $staticExpressionFactory->createBooleanExpression($nativeValue);
        }

        if (is_int($nativeValue) || is_float($nativeValue)) {
            return $staticExpressionFactory->createNumberExpression($nativeValue);
        }

        if (is_string($nativeValue)) {
            return $staticExpressionFactory->createTextExpression($nativeValue);
        }

        if ($nativeValue === null) {
            return $staticExpressionFactory->createNothingExpression();
        }

        if (is_array($nativeValue)) {
            $allNumeric = true;

            foreach ($nativeValue as $key => $elementValue) {
                if (!is_numeric($key)) {
                    $allNumeric = false;
                    break;
                }
            }

            // If all keys are numeric, coerce to a list
            if ($allNumeric) {
                return $staticExpressionFactory->createStaticListExpression(
                    $bagFactory->createStaticList(
                        array_map(
                            function ($elementValue) use (
                                $bagFactory,
                                $evaluationContext,
                                $staticExpressionFactory
                            ) {
                                return $this->coerceNative(
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

            // Otherwise coerce to a structure
            $coercedStatics = [];

            foreach ($nativeValue as $name => $attributeValue) {
                $coercedStatics[$name] = $this->coerceNative(
                    $attributeValue,
                    $staticExpressionFactory,
                    $bagFactory,
                    $evaluationContext
                );
            }

            return $staticExpressionFactory->createStaticStructureExpression(
                $bagFactory->createStaticBag($coercedStatics)
            );
        }

        throw new InvalidArgumentException(sprintf('Cannot coerce native value of type "%s"', gettype($nativeValue)));
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        return $static; // For an Any type, any value is possible
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
        return $otherType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $superType)
    {
        return $superType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return '*';
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
    public function hasValue()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        return $otherType;
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
        return $this; // Void types cannot be passed, so only keep the Any type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $otherType)
    {
        return $otherType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $otherType)
    {
        return $otherType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $otherType)
    {
        return $otherType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithAnyType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithAnyType($this);
    }
}
