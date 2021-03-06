<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
class ConfigParser implements ConfigParserInterface
{
    /**
     * @var ArgumentParserInterface
     */
    private $argumentParser;

    /**
     * @param ArgumentParserInterface $argumentParser
     */
    public function __construct(ArgumentParserInterface $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /**
     * Fetches the value of the specified config element by its key
     *
     * @deprecated Use ::parseArguments() instead.
     *
     * @param array $config
     * @param string $key
     * @param string $context
     * @param array|string $requiredType The type(s) the value must be
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

        $requiredTypes = (array) $requiredType;

        $value = $config[$key];

        if (in_array('array', $requiredTypes, true) && $value === null) {
            return [];
        }

        if (in_array(gettype($value), $requiredTypes, true)) {
            return $value;
        }

        if (in_array('number', $requiredTypes, true) && (is_int($value) || is_float($value))) {
            return $value;
        }

        throw new InvalidArgumentException(sprintf(
            'Config element "%s" should be of one of the type(s) ["%s"] but is "%s" for %s',
            $key,
            join('", "', $requiredTypes),
            gettype($value),
            $context
        ));
    }

    /**
     * Fetches the value of the specified config element by its key
     *
     * @deprecated Use ::parseArguments() instead.
     *
     * @param array $config
     * @param string $key
     * @param string $context
     * @param mixed $defaultValue Result to return if the element is not defined
     * @param array|string $requiredType The type(s) the value must be
     * @return mixed
     * @throws InvalidArgumentException Throws when the value is not specified
     * @throws InvalidArgumentException Throws when the value has the wrong type
     */
    public function getOptionalElement(array $config, $key, $context, $defaultValue = null, $requiredType = 'string')
    {
        if (!array_key_exists($key, $config)) {
            return $defaultValue;
        }

        return $this->getElement($config, $key, $context, $requiredType);
    }

    /**
     * {@inheritdoc}
     */
    public function parseArguments(array $config, array $parameterList)
    {
        return $this->argumentParser->parseArguments(
            [
                // Config arrays only support a set of named arguments,
                // as they will usually come from a set of YAML {key: value} mappings
                ArgumentParserInterface::NAMED_ARGUMENTS => $config
            ],
            $parameterList
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null) {
            return [];
        }

        throw new InvalidArgumentException(sprintf(
            'Config should be null or array but is of type "%s"',
            gettype($value)
        ));
    }
}
