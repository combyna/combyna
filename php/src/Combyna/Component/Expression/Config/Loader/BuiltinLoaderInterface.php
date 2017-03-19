<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

/**
 * Class BuiltinLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BuiltinLoaderInterface extends ExpressionLoaderInterface
{
    /**
     * Fetches the name of the builtin this loader can load
     *
     * @return string
     */
    public function getBuiltinName();
}
