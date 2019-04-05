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

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Parameter\ArgumentBagInterface;
use Combyna\Component\Config\Parameter\ParameterInterface;
use InvalidArgumentException;

/**
 * Interface ConfigParserInterface
 *
 * Encapsulates parsing data from a config array (eg. from a YAML config file)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConfigParserInterface
{
    /**
     * Parses a config array to an argument bag based on a list of parameter specifications
     *
     * @param array $config
     * @param ParameterInterface[] $parameterList
     * @return ArgumentBagInterface
     * @throws ArgumentParseException Throws when an argument is missing or invalid
     */
    public function parseArguments(array $config, array $parameterList);

    /**
     * Ensures that the value is either an array or null, returning an empty array if null
     *
     * @param mixed $value
     * @return array
     * @throws InvalidArgumentException Throws when neither null nor an array is given
     */
    public function toArray($value);
}
