<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Interface TypeTypeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypeTypeLoaderInterface
{
    /**
     * Fetches the types of type this loader can load
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Parses the given type config and creates a type structure
     *
     * @param array $config
     * @return TypeDeterminerInterface
     */
    public function load(array $config);
}
