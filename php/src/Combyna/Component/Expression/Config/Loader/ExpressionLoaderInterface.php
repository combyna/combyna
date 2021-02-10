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

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;

/**
 * Interface ExpressionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionLoaderInterface
{
    /**
     * Parses the given expression config and creates an expression ACT node structure
     *
     * @param mixed $config
     * @return ExpressionNodeInterface
     */
    public function load($config);
}
