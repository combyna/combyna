<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface DynamicContainerNodeInterface
 *
 * Represents an ACT node that can have child nodes added to it dynamically at runtime
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DynamicContainerNodeInterface extends ActNodeInterface, DynamicActNodeAdopterInterface
{
    /**
     * Determines a type for the current validation context (if any)
     *
     * @param TypeDeterminerInterface $typeDeterminer
     * @return TypeInterface
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer);
}
