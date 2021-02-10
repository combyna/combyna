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

/**
 * Interface CandidateTypeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CandidateTypeInterface
{
    /**
     * Returns true if this type is allowed by an AnyType (usually anything except an UnknownType),
     * false otherwise
     *
     * @param AnyType $superType
     * @return bool
     */
    public function isAllowedByAnyType(AnyType $superType);

    /**
     * Returns true if this type would be allowed by the specified exotic one
     *
     * @param ExoticType $otherType
     * @return bool
     */
    public function isAllowedByExoticType(ExoticType $otherType);

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
     * Returns true if this type (usually another ValuedType) would be allowed by the specified one
     *
     * @param ValuedType $otherType
     * @return bool
     */
    public function isAllowedByValuedType(ValuedType $otherType);

    /**
     * Returns true if both types are void, false otherwise
     *
     * @param VoidType $otherType
     * @return bool
     */
    public function isAllowedByVoidType(VoidType $otherType);
}
