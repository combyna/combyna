<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type;

use Combyna\Component\Expression\StaticInterface;

/**
 * Interface TypeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypeInterface
{
    /**
     * Returns true if this type is equivalent to or is a supertype of the specified type, false otherwise
     *
     * @param TypeInterface $candidateType
     * @return bool
     */
    public function allows(TypeInterface $candidateType);

    /**
     * Returns true if this type allows all sub-types of the specified multiple type, false otherwise
     *
     * @param MultipleType $candidateType
     * @param TypeInterface[] $subSubTypes
     * @return bool
     */
    public function allowsMultipleType(MultipleType $candidateType, array $subSubTypes);

    /**
     * Returns true if this type matches the specified static, false otherwise
     *
     * @param StaticInterface $static
     * @return bool
     */
    public function allowsStatic(StaticInterface $static);

    /**
     * Returns true if this type allows a list with the same element type as the provided one, false otherwise
     *
     * @param StaticListType $candidateType
     * @param TypeInterface $elementType
     * @return bool
     */
    public function allowsStaticListType(StaticListType $candidateType, TypeInterface $elementType);

    /**
     * Returns true if this type allows a static type that matches the specified one, false otherwise
     *
     * @param StaticType $candidateType
     * @return bool
     */
    public function allowsStaticType(StaticType $candidateType);

    /**
     * Returns a summary of the type represented
     * eg. ListExpression<NumberExpression|TextExpression>
     *
     * @return string
     */
    public function getSummary();

    /**
     * Returns true if this type is equivalent to the specified multiple type, false otherwise
     *
     * @param MultipleType $otherType
     * @return bool
     */
    public function isAllowedByMultipleType(MultipleType $otherType);

    /**
     * Returns true if this type is equivalent to the specified static list type, false otherwise
     *
     * @param StaticListType $otherType
     * @return bool
     */
    public function isAllowedByStaticListType(StaticListType $otherType);

    /**
     * Returns true if this type is equivalent to the specified static type, false otherwise
     *
     * @param StaticType $otherType
     * @return bool
     */
    public function isAllowedByStaticType(StaticType $otherType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided other type does
     *
     * @param TypeInterface $otherType
     * @return TypeInterface
     */
    public function mergeWith(TypeInterface $otherType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided multiple type does
     *
     * @param MultipleType $multipleType
     * @param TypeInterface[] $subSubTypes
     * @return TypeInterface
     */
    public function mergeWithMultipleType(MultipleType $multipleType, array $subSubTypes);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided static list type does
     *
     * @param StaticListType $otherType
     * @param TypeInterface $elementType
     * @return TypeInterface
     */
    public function mergeWithStaticListType(StaticListType $otherType, TypeInterface $elementType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided static type does
     *
     * @param StaticType $otherType
     * @return TypeInterface
     */
    public function mergeWithStaticType(StaticType $otherType);

    /**
     * Returns a new type that matches everything the current type does,
     * after everything the provided multiple type does
     *
     * @param MultipleType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithMultipleType(MultipleType $candidateType);

    /**
     * Returns a new type that matches everything the current type does,
     * after everything the provided static list type does
     *
     * @param StaticListType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithStaticListType(StaticListType $candidateType);

    /**
     * Returns a new type that matches everything the current type does,
     * after everything the provided static type does
     *
     * @param StaticType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithStaticType(StaticType $candidateType);
}
