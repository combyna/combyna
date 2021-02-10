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
 * Interface ExpressionTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionTypeLoaderInterface
{
    /**
     * Fetches the type of expression this loader can load
     *
     * @return string
     */
    public function getType();

    /**
     * Parses the given expression config and creates an expression ACT node structure
     *
     * @param array $config
     * @return ExpressionNodeInterface
     */
    public function load(array $config);
}
