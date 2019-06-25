<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\FileSystem;

/**
 * Interface ConfigInterface
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
interface ConfigInterface
{
    /**
     * @return array
     */
    public function toArray();
}
