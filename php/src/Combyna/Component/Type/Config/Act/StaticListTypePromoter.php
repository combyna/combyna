<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Act;

use Combyna\Component\Type\StaticListType;

/**
 * Class StaticListTypePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTypePromoter implements TypeTypePromoterInterface
{
    /**
     * @var TypePromoterInterface
     */
    private $typePromoter;

    /**
     * @param TypePromoterInterface $typePromoter
     */
    public function __construct(TypePromoterInterface $typePromoter)
    {
        $this->typePromoter = $typePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeClassToPromoterCallableMap()
    {
        return [
            StaticListType::class => [$this, 'promoteType']
        ];
    }

    /**
     * Promotes the type to one that does not contain any nested ACT nodes
     *
     * @param StaticListType $type
     * @return StaticListType
     */
    public function promoteType(StaticListType $type)
    {
        $elementType = $type->getElementType();
        $promotedElementType = $this->typePromoter->promote($elementType);

        // Create a new list type with the promoted element type if it needed to be promoted,
        // otherwise just return the original one unchanged
        return $promotedElementType !== $elementType ?
            new StaticListType($promotedElementType) :
            $type;
    }
}
