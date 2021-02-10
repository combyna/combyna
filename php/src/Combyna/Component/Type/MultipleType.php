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
use LogicException;

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
     * @param TypeInterface[] $subTypes
     */
    public function __construct(array $subTypes)
    {
        $this->subTypes = $subTypes;
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
    public function allowsVoidType(VoidType $candidateType)
    {
        return true; // Void type can be passed anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        // Coerce to the first sub-type that allows the static in its current, potentially "incomplete" form
        foreach ($this->subTypes as $subType) {
            if ($subType->allowsStatic($static)) {
                return $subType->coerceStatic($static, $evaluationContext);
            }
        }

        throw new LogicException(sprintf(
            'No sub-type allows the given static of type "%s"',
            $static->getType()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType()
    {
        return true; // Special "any" type allows any other type
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
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithMultipleType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithAnyType(AnyType $otherType)
    {
        return new MultipleType(array_merge($this->subTypes, [$otherType]));
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithMultipleType(MultipleType $otherType, array $subSubTypes)
    {
        $combinedSubTypes = array_merge($this->subTypes, $subSubTypes);

        return new MultipleType($combinedSubTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $otherType;

        return new MultipleType($combinedSubTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        $combinedSubTypes = $this->subTypes;
        $combinedSubTypes[] = $unresolvedType;

        return new MultipleType($combinedSubTypes);
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
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithMultipleType($this, $this->subTypes);
    }
}
