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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Type\TypeInterface;
use InvalidArgumentException;

/**
 * Class DelegatingTypePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingTypePromoter implements TypePromoterInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $typePromoters = [];

    /**
     * Adds a promoter for a new kind of type
     *
     * @param TypeTypePromoterInterface $typePromoter
     */
    public function addPromoter(TypeTypePromoterInterface $typePromoter)
    {
        foreach ($typePromoter->getTypeClassToPromoterCallableMap() as $typeClass => $promoterCallable) {
            $this->typePromoters[$typeClass] = $promoterCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function promote(TypeInterface $type)
    {
        $typeClass = get_class($type);

        if (!array_key_exists($typeClass, $this->typePromoters)) {
            throw new InvalidArgumentException(sprintf(
                'No promoter for types of type "%s" is registered',
                $typeClass
            ));
        }

        return $this->typePromoters[$typeClass]($type);
    }
}
