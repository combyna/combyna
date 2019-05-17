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
     * Returns true if this type is lenient enough to allow an AnyType, false otherwise
     *
     * @param AnyType $candidateType
     * @return bool
     */
    public function allowsAnyType(AnyType $candidateType);

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
     * Returns true if this type allows a structure with the same attribute bag model
     * as the provided one, false otherwise
     *
     * @param StaticStructureType $candidateType
     * @return bool
     */
    public function allowsStaticStructureType(StaticStructureType $candidateType);

    /**
     * Returns true if this type allows a static type that matches the specified one, false otherwise
     *
     * @param StaticType $candidateType
     * @return bool
     */
    public function allowsStaticType(StaticType $candidateType);

    /**
     * Returns true if this type would allow a VoidType (currently only another VoidType), false otherwise
     *
     * @param VoidType $candidateType
     * @return bool
     */
    public function allowsVoidType(VoidType $candidateType);

    /**
     * Coerces the given, potentially "incomplete" static to a "complete" one for this type
     *
     * @param StaticInterface $static
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext);

    /**
     * Returns a summary of the type represented
     * eg. `list<number|text>`
     *
     * @return string
     */
    public function getSummary();

    /**
     * Returns true if this type is allowed by an AnyType (usually anything except an UnknownType),
     * false otherwise
     *
     * @return bool
     */
    public function isAllowedByAnyType();

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
     * Determines whether the required attributes of the structure type
     * all exist in this structure (assuming this is a structure), and that there are no extras provided.
     *
     * If there are any optional attributes in the structure type, this structure
     * does not need to specify them as they can use their default values.
     *
     * @param StaticStructureType $otherType
     * @return bool
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType);

    /**
     * Returns true if this type is equivalent to the specified static type, false otherwise
     *
     * @param StaticType $otherType
     * @return bool
     */
    public function isAllowedByStaticType(StaticType $otherType);

    /**
     * Returns true if both types are void, false otherwise
     *
     * @param VoidType $otherType
     * @return bool
     */
    public function isAllowedByVoidType(VoidType $otherType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided other type does
     *
     * @param TypeInterface $otherType
     * @return TypeInterface
     */
    public function mergeWith(TypeInterface $otherType);

    /**
     * Returns a new type that will match everything the current type does and also any other type
     *
     * @param AnyType $otherType
     * @return TypeInterface
     */
    public function mergeWithAnyType(AnyType $otherType);

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
     * the provided static structure type does
     *
     * @param StaticStructureType $otherType
     * @return TypeInterface
     */
    public function mergeWithStaticStructureType(StaticStructureType $otherType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided static type does
     *
     * @param StaticType $otherType
     * @return TypeInterface
     */
    public function mergeWithStaticType(StaticType $otherType);

    /**
     * Returns a new type that will match everything the current type does and also everything
     * the provided unresolved type does
     *
     * @param UnresolvedType $unresolvedType
     * @return TypeInterface
     */
    public function mergeWithUnresolvedType(UnresolvedType $unresolvedType);

    /**
     * For void types, this will just return whichever type is not void, unless both are void
     * in which case that will be returned
     *
     * @param VoidType $otherType
     * @return TypeInterface
     */
    public function mergeWithVoidType(VoidType $otherType);

    /**
     * Returns a new type that matches everything the current type does,
     * after a special "any" type
     *
     * @param AnyType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithAnyType(AnyType $candidateType);

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
     * after everything the provided static structure type does
     *
     * @param StaticStructureType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithStaticStructureType(StaticStructureType $candidateType);

    /**
     * Returns a new type that matches everything the current type does,
     * after everything the provided static type does
     *
     * @param StaticType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithStaticType(StaticType $candidateType);

    /**
     * Returns a new type that matches everything the current type does,
     * after everything the provided unresolved type does
     *
     * @param UnresolvedType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithUnresolvedType(UnresolvedType $candidateType);

    /**
     * For void types, this will just return whichever type is not void, unless both are void
     * in which case that will be returned
     *
     * @param VoidType $candidateType
     * @return TypeInterface
     */
    public function whenMergedWithVoidType(VoidType $candidateType);
}
