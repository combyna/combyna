<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Promoter;

/**
 * Interface WidgetNodeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetNodeTypePromoterInterface
{
    /**
     * Fetches a map from core widget type name to the promoter callable on this service
     *
     * @return array
     */
    public function getTypeToPromoterCallableMap();
}
