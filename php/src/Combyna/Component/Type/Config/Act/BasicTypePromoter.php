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
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\ExoticType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Type\VoidType;

/**
 * Class BasicTypePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BasicTypePromoter implements TypeTypePromoterInterface
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
            AnyType::class => [$this, 'promoteType'],
            ExoticType::class => [$this, 'promoteType'],
            StaticType::class => [$this, 'promoteType'],
            UnresolvedType::class => [$this, 'promoteType'],
            VoidType::class => [$this, 'promoteType']
        ];
    }

    /**
     * Promotes the type to one that does not contain any nested ACT nodes
     * (nothing to do for these types, so we just return the original unchanged)
     *
     * @param TypeInterface $type
     * @return TypeInterface
     */
    public function promoteType(TypeInterface $type)
    {
        return $type;
    }
}
