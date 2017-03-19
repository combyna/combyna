<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Loader;

use InvalidArgumentException;

/**
 * Class ConfigParser
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigParser
{
    /**
     * Fetches the value of the specified config element by its key
     *
     * @param array $config
     * @param string $key
     * @param string $context
     * @return mixed
     * @throws InvalidArgumentException Throws when the argument is not passed
     */
    public function getElement(array $config, $key, $context)
    {
        if (!array_key_exists($key, $config)) {
            throw new InvalidArgumentException(
                'Missing required "' . $key . '" config for ' . $context
            );
        }

        return $config[$key];
    }
}
