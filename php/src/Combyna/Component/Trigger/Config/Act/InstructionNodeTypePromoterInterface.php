<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

/**
 * Interface InstructionNodeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionNodeTypePromoterInterface
{
    /**
     * Fetches a map from instruction type name to the promoter method on this service
     *
     * @return array
     */
    public function getTypeToPromoterMethodMap();
}
