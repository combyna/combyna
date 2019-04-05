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

/**
 * Interface ArgumentParserInterface
 *
 * Encapsulates parsing arguments from a config array and parameter list
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ArgumentParserInterface
{
    const NAMED_ARGUMENTS = 'named-arguments';
    const POSITIONAL_ARGUMENTS = 'positional-arguments';

    /**
     * Parses a config array to an argument bag based on a list of parameter specifications
     *
     * @param array $config
     * @param ParameterInterface[] $parameterList
     * @return ArgumentBagInterface
     * @throws ArgumentParseException Throws when an argument is missing or invalid
     */
    public function parseArguments(array $config, array $parameterList);
}
