<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter;

use Combyna\Component\Config\Exception\ArgumentMismatchExceptionInterface;

/**
 * Interface ParameterParserInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterParserInterface
{
    /**
     * Parses the argument for a parameter
     *
     * @param ParameterInterface $parameter
     * @param array $config
     * @param ParameterInterface[] $parameterList
     * @param array $extraArguments
     * @return mixed
     */
    public function parseArgument(
        ParameterInterface $parameter,
        array $config,
        array $parameterList,
        array $extraArguments
    );

    /**
     * Checks whether the argument for a parameter is valid, if given
     *
     * @param ParameterInterface $parameter
     * @param array $config
     * @throws ArgumentMismatchExceptionInterface
     */
    public function validateArgument(
        ParameterInterface $parameter,
        array $config
    );
}
