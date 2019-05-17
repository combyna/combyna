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

use Combyna\Component\Type\TypeInterface;

/**
 * Interface TypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypePromoterInterface
{
    /**
     * Promotes the provided type, which may contain nested ACT nodes,
     * to one that is guaranteed not to. For example, it may promote
     * a StaticStructureType that contains a DeterminedFixedStaticBagModelNode
     * to one that contains a FixedStaticBagModel.
     *
     * @param TypeInterface $type
     * @return TypeInterface
     */
    public function promote(TypeInterface $type);
}
