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
use Combyna\Component\Config\Exception\WrongArgumentTypeException;
use Combyna\Component\Config\Loader\ArgumentParser;
use Combyna\Component\Config\Parameter\Type\ParameterTypeParserInterface as ParameterTypeParser;

/**
 * Class NamedParameterParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NamedParameterParser implements ParameterTypeParserInterface
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
            NamedParameter::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToValidatorCallableMap()
    {
        return [
            NamedParameter::TYPE => [$this, 'validateArgument']
        ];
    }

    /**
     * Parses an argument for this parameter to its value
     *
     * @param NamedParameter $parameter
     * @param array $config
     * @return mixed
     * @throws WrongArgumentTypeException
     * @throws RequiredArgumentMissingException
     */
    public function parseArgument(
        NamedParameter $parameter,
        array $config
    ) {
        $this->validateArgument($parameter, $config);

        $namedArguments = array_key_exists(ArgumentParser::NAMED_ARGUMENTS, $config) ?
            $config[ArgumentParser::NAMED_ARGUMENTS] :
            [];

        return $this->typeParser->parseArgument(
            $parameter->getParameterType(),
            $namedArguments[$parameter->getName()]
        );
    }

    /**
     * Validates an argument for this parameter
     *
     * @param NamedParameter $parameter
     * @param array $config
     * @throws WrongArgumentTypeException
     * @throws RequiredArgumentMissingException
     */
    public function validateArgument(NamedParameter $parameter, array $config)
    {
        $namedArguments = array_key_exists(ArgumentParser::NAMED_ARGUMENTS, $config) ?
            $config[ArgumentParser::NAMED_ARGUMENTS] :
            [];

        if (
            !array_key_exists($parameter->getName(), $namedArguments) ||
            // Treat arguments set to null as though they were not specified at all
            // so that we can fall back to evaluating their default value
            $namedArguments[$parameter->getName()] === null
        ) {
            throw new RequiredArgumentMissingException($parameter->getName(), array_keys($namedArguments));
        }

        $value = $namedArguments[$parameter->getName()];

        if (!$this->typeParser->argumentIsValid($parameter->getParameterType(), $value)) {
            throw new WrongArgumentTypeException($parameter->getName(), $parameter->getParameterType(), $value);
        }
    }
}
