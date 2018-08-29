<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Compiler\Pass;

/**
 * Interface BehaviourSpecPassInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecPassInterface
{
    /**
     * Fetches the map from a structured node class
     * to the callback that should be called for each instance of it
     *
     * @return callable[]
     */
    public function getNodeClassToVisitorCallableMap();
}
