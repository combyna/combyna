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

/**
 * Interface TypeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypeTypePromoterInterface
{
    /**
     * Fetches a map from type name to the promoter callable on this service
     *
     * @return array
     */
    public function getTypeClassToPromoterCallableMap();
}
