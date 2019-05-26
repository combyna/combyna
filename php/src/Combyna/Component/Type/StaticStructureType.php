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

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticBagModelInterface;
use Combyna\Component\Bag\FixedStaticDefinitionInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticStructureExpression;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;
use LogicException;

/**
 * Class StaticStructureType
 *
 * Matches an expression whose eventual result static is a static structure
 * with a specific set of attributes with defined types of value
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureType implements TypeInterface
{
    /**
     * @var DeterminedFixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @param DeterminedFixedStaticBagModelInterface $attributeBagModel
     */
    public function __construct(DeterminedFixedStaticBagModelInterface $attributeBagModel)
    {
        $this->attributeBagModel = $attributeBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsAnyType(AnyType $candidateType)
    {
        return false; // A static structure can only match specific other types, not "any" type
    }

    /**
     * {@inheritdoc}
     */
    public function allowsMultipleType(MultipleType $candidateType, array $subSubTypes)
    {
        foreach ($subSubTypes as $subSubType) {
            if (!$this->allows($subSubType)) {
                // One of the sub-types of the multiple one is not allowed
                // by this super static structure type, so we don't allow
                return false;
            }
        }

        // All sub-types of the multiple one are allowed by this super static structure type
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        if (!$static instanceof StaticStructureExpression) {
            return false;
        }

        return $static->attributesMatch($this->attributeBagModel);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType)
    {
        return false; // We require a structure, not a list
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType)
    {
        // As long as the candidate sub-static-structure type's attribute bag model is allowed
        // by our own attribute bag model, then let it through.
        return $this->attributeBagModel->allowsOtherModel($candidateType->getAttributeBagModel());
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticType(StaticType $candidateType)
    {
        return false; // We require a structure, which a non-structure static type can never be
    }

    /**
     * {@inheritdoc}
     */
    public function allowsValuedType(ValuedType $candidateType)
    {
        // Discard the value of the valued type and just check whether
        // this structure type allows the value's wrapped type through
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
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        if (!$static instanceof StaticStructureExpression) {
            throw new LogicException(sprintf(
                'Expected %s, got %s',
                StaticStructureExpression::class,
                get_class($static)
            ));
        }

        /** @var FixedStaticDefinitionInterface[] $staticDefinitions */
        $staticDefinitions = $this->attributeBagModel->getStaticDefinitions();
        $completeStatics = [];
        $wasComplete = true;

        foreach ($staticDefinitions as $name => $staticDefinition) {
            if ($static->hasAttributeStatic($name)) {
                $completeStatics[$name] = $staticDefinition->coerceStatic(
                    $evaluationContext,
                    $static->getAttributeStatic($name)
                );
            } else {
                $completeStatics[$name] = $staticDefinition->getDefaultStatic($evaluationContext);
                $wasComplete = false;
            }
        }

        if ($wasComplete) {
            return $static; // Just return the original structure if it was complete
        }

        return $static->withStatics($completeStatics);
    }

    /**
     * Fetches the fixed static bag model for the attributes of this structure
     *
     * @return DeterminedFixedStaticBagModelInterface
     */
    public function getAttributeBagModel()
    {
        return $this->attributeBagModel;
    }

    /**
     * Fetches the type of one of the attributes of this structure.
     * Will return an UnresolvedType if the attribute does not exist
     *
     * @param string $attributeName
     * @return TypeInterface
     */
    public function getAttributeType($attributeName)
    {
        return $this->attributeBagModel->getStaticDefinitionByName($attributeName, new NullActNodeAdopter())
            ->getStaticType();
    }

    /**
     * Determines whether the structure defines an attribute with the given name
     *
     * @param string $attributeName
     * @return bool
     */
    public function hasAttribute($attributeName)
    {
        return $this->attributeBagModel->definesStatic($attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $superType)
    {
        return $superType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $superType)
    {
        return $superType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $otherType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $otherType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $otherType->allowsStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return 'structure<' . $this->attributeBagModel->getSummary() . '>';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        return 'structure<' . $this->attributeBagModel->getSummaryWithValue() . '>';
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        return $this->attributeBagModel->hasValue();
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithStaticStructureType($this);
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
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType)
    {
        // Unlike StaticListType, it won't always be possible to neatly merge two structures.
        // We could in future attempt to match the two for similarity, and if within a certain %,
        // attempt to define a single new structure type where each attribute type is lenient enough
        // to cover the types from both of the source structures.
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        // There is nothing common to merge between a static structure type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        // There is nothing common to merge between a static structure type and an unresolved type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $unresolvedType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithValuedType(ValuedType $otherType)
    {
        // There is nothing common to merge between a static structure type and a valued type,
        // so just return a MultipleType that allows both
        // TODO: Consider merging this type with the valued type's wrapped type
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithVoidType(VoidType $otherType)
    {
        return $this; // Void types cannot be passed, so only keep the static structure type
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithAnyType(AnyType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithMultipleType(MultipleType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticListType(StaticListType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithStaticType(StaticType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithValuedType(ValuedType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function whenMergedWithVoidType(VoidType $candidateType)
    {
        return $candidateType->mergeWithStaticStructureType($this);
    }
}
