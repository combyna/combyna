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
 * Class BuiltinLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BuiltinLoaderInterface
{
    /**
     * Fetches the name of the builtin this loader can load
     *
     * @return string
     */
    public function getBuiltinName();

    /**
     * Parses the given expression config and creates an expression ACT node structure
     *
     * @param array|string $config
     * @return ExpressionNodeInterface
     */
    public function load(array $config);
}
