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
 * Class MultipleType
 *
 * Matches multiple different types of eventual return value static of expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleType implements TypeInterface
{
    /**
     * @var TypeInterface[]
     */
    private $subTypes;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param TypeInterface[] $subTypes
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(array $subTypes, ValidationContextInterface $validationContext)
    {
        $this->subTypes = $subTypes;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByMultipleType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        // Check that at least one of our sub-types allows the "any" type
        foreach ($this->subTypes as $ourSubType) {
            if ($ourSubType->allows($candidateType)) {
                return true;
            }
        }

        return false;
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
        // Check that each sub-type of the candidate type is allowed by at least one sub-type of this super type
        // - this super type may match additional types that the sub-one does not allow
        foreach ($subSubTypes as $theirSubType) {
            $matchFound = false;

            foreach ($this->subTypes as $ourSubType) {
                if ($ourSubType->allows($theirSubType)) {
                    $matchFound = true;
                    break;
                }
            }

            if (!$matchFound) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        foreach ($this->subTypes as $subType) {
            // If any sub-type of this type matches the static then it is matched
            if ($subType->allowsStatic($static)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        // Check that at least one of our sub-types allows the static list type
        foreach ($this->subTypes as $ourSubType) {
            if ($ourSubType->allows($candidateType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        // Check that at least one of our sub-types allows the static structure type
        foreach ($this->subTypes as $ourSubType) {
            if ($ourSubType->allows($candidateType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        // Check that at least one of our sub-types allows the static type
        foreach ($this->subTypes as $ourSubType) {
            if ($ourSubType->allows($candidateType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        // Check that at least one of our sub-types allows the valued type
        foreach ($this->subTypes as $ourSubType) {
            if ($ourSubType->allows($candidateType)) {
                return true;
            }
        }

        return false;
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
                        'Static of type "%s" was given, expected a native or static of type "%s"',
                        $nativeValue->getType(),
                        $this->getSummary()
                    )
                );
            }

            // Already a static, but we still need to perform static coercion
            // so that any incomplete statics may be completed
            return $this->coerceStatic($nativeValue, $evaluationContext);
        }

        foreach ($this->subTypes as $subType) {
            try {
                return $subType->coerceNative(
                    $nativeValue,
                    $staticExpressionFactory,
                    $bagFactory,
                    $evaluationContext
                );
            } catch (IncompatibleNativeForCoercionException $exception) {
                // Ignore the exception, try the next sub-type to see if it can coerce
            }
        }

        // None of the sub-types could coerce
        throw new IncompatibleNativeForCoercionException('No sub-types were able to coerce the value');
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        // Coerce to the first sub-type that allows the static in its current, potentially "incomplete" form
        foreach ($this->subTypes as $subType) {
            try {
                return $subType->coerceStatic($static, $evaluationContext);
            } catch (IncompatibleStaticForCoercionException $exception) {
                // Ignore and try the next sub-type
            }
        }

        throw new IncompatibleStaticForCoercionException(sprintf(
            'No sub-type allows the given static of type "%s"',
            $static->getType()
        ));
    }

    /**
     * Fetches all sub-types of this multiple one
     *
     * @return TypeInterface[]
     */
    public function getSubTypes()
    {
        return $this->subTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType(AnyType $superType)
    {
        return true; // Special "any" type allows any other type
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $otherType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $otherType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $parts = [];

        foreach ($this->subTypes as $subType) {
            $parts[] = $subType->getSummary();
        }

        return implode('|', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        $parts = [];

        foreach ($this->subTypes as $subType) {
            $parts[] = $subType->getSummaryWithValue(); // Same as ->getSummary(), but with values
        }

        return implode('|', $parts);
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
        foreach ($this->subTypes as $subType) {
            if ($subType->hasValue()) {
                // If any sub-type (or descendant of one, like an attribute of a structure type)
                // has a value, then treat the whole group as storing value information
                // so that it may be displayed
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithMultipleType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        return new MultipleType(array_merge($this->subTypes, [$otherType]), $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithExoticType(ExoticType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithMultipleType(MultipleType $otherType, array $subSubTypes)
    {
        $combinedSubTypes = array_merge($this->subTypes, $subSubTypes);

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $unresolvedType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes, $this->validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the Multiple type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $otherType)
    {
        return $otherType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithExoticType(ExoticType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $otherType)
    {
        return $otherType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $otherType)
    {
        return $otherType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }
}
