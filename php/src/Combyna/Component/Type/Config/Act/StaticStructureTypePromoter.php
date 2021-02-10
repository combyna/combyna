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

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Validator\Context\NullValidationContext;
use LogicException;

/**
 * Class StaticStructureTypePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureTypePromoter implements TypeTypePromoterInterface
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(BagNodePromoter $bagNodePromoter)
    {
        $this->bagNodePromoter = $bagNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeClassToPromoterCallableMap()
    {
        return [
            StaticStructureType::class => [$this, 'promoteType']
        ];
    }

    /**
     * Promotes the type to one that does not contain any nested ACT nodes
     *
     * @param StaticStructureType $type
     * @return StaticStructureType
     */
    public function promoteType(StaticStructureType $type)
    {
        $attributeBagModel = $type->getAttributeBagModel();

        if (!$attributeBagModel instanceof FixedStaticBagModelNodeInterface) {
            throw new LogicException(sprintf(
                'Could not promote structure type - expected %s but got %s',
                FixedStaticBagModelNodeInterface::class,
                get_class($attributeBagModel)
            ));
        }

        $promotedAttributeBagModel = $this->bagNodePromoter->promoteFixedStaticBagModel($attributeBagModel);

        return new StaticStructureType($promotedAttributeBagModel, new NullValidationContext());
    }
}
