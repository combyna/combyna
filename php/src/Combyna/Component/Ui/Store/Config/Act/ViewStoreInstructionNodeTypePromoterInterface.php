<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

/**
 * Interface ViewStoreInstructionNodeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreInstructionNodeTypePromoterInterface
{
    /**
     * Fetches a map from instruction type name to the promoter method on this service
     *
     * @return array
     */
    public function getTypeToPromoterMethodMap();
}
