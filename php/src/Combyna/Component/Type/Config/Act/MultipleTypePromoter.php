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

use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\TypeInterface;

/**
 * Class MultipleTypePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleTypePromoter implements TypeTypePromoterInterface
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
            MultipleType::class => [$this, 'promoteType']
        ];
    }

    /**
     * Promotes the type to one that does not contain any nested ACT nodes
     *
     * @param MultipleType $type
     * @return MultipleType
     */
    public function promoteType(MultipleType $type)
    {
        $promotedSubTypes = array_map(
            function (TypeInterface $subType) {
                return $this->typePromoter->promote($subType);
            },
            $type->getSubTypes()
        );

        if ($promotedSubTypes === $type->getSubTypes()) {
            // No sub-types needed promotion, so just return the original multiple type
            return $type;
        }

        return new MultipleType($promotedSubTypes, $type->getValidationContext());
    }
}
