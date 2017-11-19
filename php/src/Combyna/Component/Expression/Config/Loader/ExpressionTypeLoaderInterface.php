<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

/**
 * Interface ExpressionTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionTypeLoaderInterface extends ExpressionLoaderInterface
{
    /**
     * Fetches the type of expression this loader can load
     *
     * @return string
     */
    public function getType();
}
