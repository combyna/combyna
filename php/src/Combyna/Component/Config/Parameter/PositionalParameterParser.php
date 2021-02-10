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
 * Class PositionalParameterParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PositionalParameterParser implements ParameterTypeParserInterface
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
            PositionalParameter::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToValidatorCallableMap()
    {
        return [
            PositionalParameter::TYPE => [$this, 'validateArgument']
        ];
    }

    /**
     * Parses an argument for this parameter to its value
     *
     * @param PositionalParameter $parameter
     * @param array $config
     * @return mixed
     * @throws WrongArgumentTypeException
     * @throws RequiredArgumentMissingException
     */
    public function parseArgument(
        PositionalParameter $parameter,
        array $config
    ) {
        $this->validateArgument($parameter, $config);

        $positionalArguments = array_key_exists(ArgumentParser::POSITIONAL_ARGUMENTS, $config) ?
            $config[ArgumentParser::POSITIONAL_ARGUMENTS] :
            [];

        return $this->typeParser->parseArgument(
            $parameter->getParameterType(),
            $positionalArguments[$parameter->getPosition()]
        );
    }

    /**
     * Validates an argument for this parameter
     *
     * @param PositionalParameter $parameter
     * @param array $config
     * @throws WrongArgumentTypeException
     * @throws RequiredArgumentMissingException
     */
    public function validateArgument(PositionalParameter $parameter, array $config)
    {
        $positionalArguments = array_key_exists(ArgumentParser::POSITIONAL_ARGUMENTS, $config) ?
            $config[ArgumentParser::POSITIONAL_ARGUMENTS] :
            [];

        if (
            !array_key_exists($parameter->getPosition(), $positionalArguments) ||
            // Treat arguments set to null as though they were not specified at all
            // so that we can fall back to evaluating their default value
            $positionalArguments[$parameter->getPosition()] === null
        ) {
            throw new RequiredArgumentMissingException(
                sprintf(
                    '%s (#%s)',
                    $parameter->getName(),
                    $parameter->getPosition()
                ),
                array_keys($positionalArguments)
            );
        }

        $value = $positionalArguments[$parameter->getPosition()];

        if (!$this->typeParser->argumentIsValid($parameter->getParameterType(), $value)) {
            throw new WrongArgumentTypeException(
                sprintf(
                    '%s (#%s)',
                    $parameter->getName(),
                    $parameter->getPosition()
                ),
                $parameter->getParameterType(),
                $value
            );
        }
    }
}
