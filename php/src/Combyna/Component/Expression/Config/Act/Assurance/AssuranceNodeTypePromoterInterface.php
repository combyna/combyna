<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

/**
 * Interface AssuranceNodeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceNodeTypePromoterInterface
{
    /**
     * Fetches a map from assurance type name to the promoter callable on this service
     *
     * @return array
     */
    public function getTypeToPromoterCallableMap();
}
