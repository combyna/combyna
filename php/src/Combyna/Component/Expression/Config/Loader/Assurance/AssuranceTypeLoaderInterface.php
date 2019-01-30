<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader\Assurance;

/**
 * Interface AssuranceTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceTypeLoaderInterface extends AssuranceLoaderInterface
{
    /**
     * Fetches the constraint for the type of assurance this loader can load
     *
     * @return string
     */
    public function getConstraint();
}
