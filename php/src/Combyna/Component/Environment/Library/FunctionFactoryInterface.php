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

/**
 * Interface FunctionFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FunctionFactoryInterface
{
    /**
     * Creates a new FunctionCollection
     *
     * @param FunctionInterface[] $functions
     * @param string $libraryName
     * @return FunctionCollectionInterface
     */
    public function createCollection(array $functions, $libraryName);
}
