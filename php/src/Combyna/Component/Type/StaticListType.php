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

use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticListExpression;

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
     * @param TypeInterface $elementType Type allowed for elements of the list
     */
    public function __construct(TypeInterface $elementType)
    {
        $this->elementType = $elementType;
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
        // by our own element type, then let it through
        return $this->elementType->allows($elementType);
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
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsStaticListType($this, $this->elementType);
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
    public function mergeWith(TypeInterface $otherType)
    {
        return $otherType->whenMergedWithStaticListType($this);
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
        // To merge two static list types together, create a new static list type
        // whose element type encompasses this one's element type and the other type's element type
        $combinedElementType = $this->elementType->mergeWith($elementType);

        return new StaticListType($combinedElementType);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithStaticType(StaticType $otherType)
    {
        // There is nothing common to merge between a static list type and a static type,
        // so just return a MultipleType that allows both
        return new MultipleType([$this, $otherType]);
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
    public function whenMergedWithStaticType(StaticType $otherType)
    {
        return $otherType->mergeWithStaticListType($this, $this->elementType);
    }
}
