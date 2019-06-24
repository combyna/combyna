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
use Combyna\Component\Type\Exotic\Determination\TypeDeterminationInterface;
use Combyna\Component\Type\Exotic\ExoticTypeDeterminerInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ExoticType
 *
 * Represents a type that depends on either the "candidate" type being passed to it
 * or the type of another relevant candidate (eg. a sibling expression,
 * like the relationship between a route name and a bag of arguments for its parameters)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExoticType implements TypeInterface
{
    /**
     * @var ExoticTypeDeterminerInterface
     */
    private $exoticTypeDeterminer;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ExoticTypeDeterminerInterface $exoticTypeDeterminer
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        ExoticTypeDeterminerInterface $exoticTypeDeterminer,
        ValidationContextInterface $validationContext
    ) {
        $this->exoticTypeDeterminer = $exoticTypeDeterminer;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsExoticType(ExoticType $candidateType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsMultipleType(MultipleType $candidateType, array $subSubTypes)
    {
        foreach ($subSubTypes as $subSubType) {
            if (!$this->allows($subSubType)) {
                // One of the sub-types of the multiple one is not allowed
                // by this super exotic type, so we don't allow
                return false;
            }
        }

        // All sub-types of the multiple one are allowed by this super exotic type
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        // Without knowing the candidate type, we cannot resolve to an actual type
        // in order to test whether the given static would be allowed. In practice
        // this should not be an issue as the places where runtime type checking
        // is performed (eg. on native function return values),
        // the type should not be an exotic one anyway
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        // Make sure we do _not_ discard the valued type's value and pass it through unmodified,
        // so that the exotic type determiner is able to inspect any value information
        return $this
            ->determineExoticType($candidateType)
            ->allowsType($candidateType);
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
        return $this->exoticTypeDeterminer->coerceNative(
            $nativeValue,
            $staticExpressionFactory,
            $bagFactory,
            $evaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        return $this->exoticTypeDeterminer->coerceStatic(
            $static,
            $evaluationContext
        );
    }

    /**
     * Determines the type that this exotic type resolves to
     *
     * @param TypeInterface $candidateType
     * @return TypeDeterminationInterface
     */
    public function determineExoticType(TypeInterface $candidateType)
    {
        return $this->exoticTypeDeterminer
            ->determine($this, $candidateType);
    }

    /**
     * Fetches the name of the exotic type determiner for this exotic type
     *
     * @return string
     */
    public function getExoticTypeName()
    {
        return $this->exoticTypeDeterminer->getName();
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
        return $this
            ->determineExoticType($superType)
            ->isAllowedByAnyType($superType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $this
            ->determineExoticType($otherType)
            ->isAllowedByExoticType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $this
            ->determineExoticType($superType)
            ->isAllowedByMultipleType($superType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $this
            ->determineExoticType($superType)
            ->isAllowedByStaticListType($superType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $this
            ->determineExoticType($otherType)
            ->isAllowedByStaticStructureType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $this
            ->determineExoticType($superType)
            ->isAllowedByStaticType($superType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $this
            ->determineExoticType($otherType)
            ->isAllowedByValuedType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $this
            ->determineExoticType($otherType)
            ->isAllowedByVoidType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'exotic<' . $this->exoticTypeDeterminer->getName() . '>';
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
        // No value information readily available, but exotic type determiners almost always need
        // value information to match against
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithExoticType($this);
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
        // Don't attempt to merge exotic types, even if they use the same determiner name
        // as their config and/or behaviour in a specific validation context may differ
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
        // There is nothing common to merge between an exotic type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        // There is nothing common to merge between an exotic type and an unresolved type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $unresolvedType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        // There is nothing common to merge between an exotic type and a valued type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType], $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the exotic type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithExoticType($this);
    }
}
