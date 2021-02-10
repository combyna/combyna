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

use Combyna\Component\Config\Exception\ArgumentMismatchExceptionInterface;
use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Parameter\ArgumentBag;
use Combyna\Component\Config\Parameter\ExtraParameter;
use Combyna\Component\Config\Parameter\ParameterInterface;
use Combyna\Component\Config\Parameter\ParameterParserInterface;
use Combyna\Component\Config\Parameter\PositionalParameter;

/**
 * Class ArgumentParser
 *
 * Encapsulates parsing arguments from a config array and parameter list
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArgumentParser implements ArgumentParserInterface
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
    public function parseArguments(array $config, array $parameterList)
    {
        $namedArguments = array_key_exists(ArgumentParser::NAMED_ARGUMENTS, $config) ?
            $config[ArgumentParser::NAMED_ARGUMENTS] :
            [];
        $parameterNames = array_map(function (ParameterInterface $parameter) {
            return $parameter->getName();
        }, $parameterList);

        $extraArgumentsAreAllowed = false;
        $extraArguments = [];

        foreach (array_diff(array_keys($namedArguments), $parameterNames) as $parameterName) {
            $extraArguments[$parameterName] = $namedArguments[$parameterName];
        }

        $arguments = [];
        $argumentMismatches = [];
        $nextPositionalParameterIndex = 0;

        foreach ($parameterList as $parameter) {
            if ($parameter instanceof PositionalParameter) {
                // Positional parameters need to be provided with their position index
                // so that they can extract the correct argument from the list
                $parameter->setPosition($nextPositionalParameterIndex++);
            }

            try {
                $arguments[$parameter->getName()] = $this->parameterParser->parseArgument(
                    $parameter,
                    $config,
                    $parameterList,
                    $extraArguments
                );

                if ($parameter instanceof ExtraParameter) {
                    $extraArgumentsAreAllowed = true;
                }
            } catch (ArgumentMismatchExceptionInterface $exception) {
                $argumentMismatches[] = $exception->getMessage();
            }
        }

        // Check whether any extra arguments were given when no extra parameter was specified
        if (!$extraArgumentsAreAllowed && count($extraArguments) > 0) {
            $argumentMismatches[] = sprintf(
                'Extra arguments not allowed: [%s]',
                implode(', ', array_keys($extraArguments))
            );
        }

        if (count($argumentMismatches) > 0) {
            // Combine all the mismatch exception messages together into one new exception,
            // which can then be included in a validation failure message
            throw new ArgumentParseException(implode(' :: ', $argumentMismatches));
        }

        return new ArgumentBag($arguments, $extraArguments);
    }
}
