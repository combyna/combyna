<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Type\TypeInterface;

/**
 * Interface TypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypeLoaderInterface
{
    /**
     * Parses the given type config and creates a type structure
     *
     * @param array|string $config
     * @return TypeInterface
     */
    public function load($config);
}
