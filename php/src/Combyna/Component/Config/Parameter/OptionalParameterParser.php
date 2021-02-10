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

use Combyna\Component\Config\Exception\RequiredArgumentMissingException;

/**
 * Class OptionalParameterParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class OptionalParameterParser implements ParameterTypeParserInterface
{
    /**
     * @var ParameterParserInterface
     */
    private $parameterParser;

    /**
     * @param ParameterParserInterface $parameterParser
     */
    public function __construct(ParameterParserInterface $parameterParser)
    {
        $this->parameterParser = $parameterParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            OptionalParameter::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToValidatorCallableMap()
    {
        return [
            OptionalParameter::TYPE => [$this, 'validateArgument']
        ];
    }

    /**
     * Parses an argument for this parameter to its value
     *
     * @param OptionalParameter $parameter
     * @param array $config
     * @param array $parameterList
     * @param array $extraArguments
     * @return mixed
     */
    public function parseArgument(
        OptionalParameter $parameter,
        array $config,
        array $parameterList,
        array $extraArguments
    ) {
        try {
            return $this->parameterParser->parseArgument(
                $parameter->getWrappedParameter(),
                $config,
                $parameterList,
                $extraArguments
            );
        } catch (RequiredArgumentMissingException $exception) {
            // Argument is not specified, so fall back to the default value
            return $parameter->getDefaultValue();
        }
    }

    /**
     * Validates an argument for this parameter
     *
     * @param OptionalParameter $parameter
     * @param array $config
     */
    public function validateArgument(OptionalParameter $parameter, array $config)
    {
        try {
            $this->parameterParser->validateArgument(
                $parameter->getWrappedParameter(),
                $config
            );
        } catch (RequiredArgumentMissingException $exception) {
            // Ignore this exception, as this argument is optional
        }
    }
}
