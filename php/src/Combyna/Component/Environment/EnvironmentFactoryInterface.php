<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Interface EnvironmentFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EnvironmentFactoryInterface
{
    /**
     * Creates a new Environment
     *
     * @param LibraryInterface[] $libraries
     * @return Environment
     */
    public function create(array $libraries = []);
}
