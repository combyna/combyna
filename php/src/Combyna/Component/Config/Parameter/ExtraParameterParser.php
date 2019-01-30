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

use Combyna\Component\Config\Parameter\Type\ParameterTypeParserInterface as ParameterTypeParser;

/**
 * Class ExtraParameterParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExtraParameterParser implements ParameterTypeParserInterface
{
    /**
     * @var ParameterTypeParser
     */
    private $typeParser;

    public function __construct(ParameterTypeParser $typeParser)
    {
        $this->typeParser = $typeParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            ExtraParameter::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToValidatorCallableMap()
    {
        return [
            ExtraParameter::TYPE => [$this, 'validateArgument']
        ];
    }

    /**
     * Parses an argument for this parameter to its value
     *
     * @param ExtraParameter $parameter
     * @param array $config
     * @param array $parameterList
     * @param array $extraArguments
     * @return mixed
     */
    public function parseArgument(
        ExtraParameter $parameter,
        array $config,
        array $parameterList,
        array $extraArguments
    ) {
        return $extraArguments;
    }

    /**
     * Validates an argument for this parameter
     *
     * @param ExtraParameter $parameter
     * @param array $config
     */
    public function validateArgument(ExtraParameter $parameter, array $config)
    {
        // The extra arguments are untyped and optional, so they can never be invalid
    }
}
