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
     * {@inheritdoc}
     */
    public function allows(TypeInterface $candidateType)
    {
        return $candidateType->isAllowedByAnyType();
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
    public function allowsStaticType(StaticType $candidateType)
    {
        return true; // Any static type is allowed
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
    public function isAllowedByStaticType(StaticType $superType)
    {
        return $superType->allowsAnyType($this);
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
    public function mergeWithStaticType(StaticType $otherType)
    {
        return new MultipleType([$this, $otherType]);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType)
    {
        return new MultipleType([$this, $unresolvedType]);
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
}
