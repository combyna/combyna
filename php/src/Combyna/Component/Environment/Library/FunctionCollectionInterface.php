<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Environment\Exception\FunctionNotSupportedException;

/**
 * Interface FunctionCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FunctionCollectionInterface
{
    /**
     * Fetches a function by its unique name
     *
     * @param string $functionName
     * @return FunctionInterface
     * @throws FunctionNotSupportedException Throws when no function has the specified name
     */
    public function getByName($functionName);
}
