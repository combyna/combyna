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
     * @param string $requiredType The type the value must be
     * @return mixed
     * @throws InvalidArgumentException Throws when the value is not specified
     * @throws InvalidArgumentException Throws when the value has the wrong type
     */
    public function getElement(array $config, $key, $context, $requiredType = 'string')
    {
        if (!array_key_exists($key, $config)) {
            throw new InvalidArgumentException(
                'Missing required "' . $key . '" config for ' . $context
            );
        }

        $value = $config[$key];

        if ($requiredType === 'array' && $value === null) {
            return [];
        }

        if (gettype($value) !== $requiredType) {
            throw new InvalidArgumentException(sprintf(
                'Config element "%s" should be of type "%s" but is "%s" for %s',
                $key,
                $requiredType,
                gettype($value),
                $context
            ));
        }

        return $value;
    }

    /**
     * Fetches the value of the specified config element by its key
     *
     * @param array $config
     * @param string $key
     * @param string $context
     * @param mixed $defaultValue Result to return if the element is not defined
     * @param string $requiredType The type the value must be
     * @return mixed
     * @throws InvalidArgumentException Throws when the value is not specified
     * @throws InvalidArgumentException Throws when the value has the wrong type
     */
    public function getOptionalElement(array $config, $key, $context, $defaultValue = null, $requiredType = 'string')
    {
        if (!array_key_exists($key, $config)) {
            return $defaultValue;
        }

        $value = $config[$key];

        if ($requiredType === 'array' && $value === null) {
            return [];
        }

        if (gettype($value) !== $requiredType) {
            throw new InvalidArgumentException(sprintf(
                'Config element "%s" should be of type "%s" but is "%s" for %s',
                $key,
                $requiredType,
                gettype($value),
                $context
            ));
        }

        return $value;
    }
}
