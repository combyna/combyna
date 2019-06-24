<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic;

/**
 * Interface ExoticTypeTypeDeterminerFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExoticTypeTypeDeterminerFactoryInterface
{
    /**
     * Fetches a map from exotic type name to the factory callable on this service
     *
     * @return array
     */
    public function getTypeNameToFactoryCallableMap();
}
