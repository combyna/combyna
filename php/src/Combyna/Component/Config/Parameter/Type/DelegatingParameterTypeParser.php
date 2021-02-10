<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter\Type;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use InvalidArgumentException;

/**
 * Class DelegatingParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingParameterTypeParser implements ParameterTypeParserInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $parameterTypeParsers = [];

    /**
     * @var callable[]
     */
    private $parameterValidityCheckers = [];

    /**
     * @param ParameterTypeTypeParserInterface $parameterTypeTypeParser
     */
    public function addParser(ParameterTypeTypeParserInterface $parameterTypeTypeParser)
    {
        foreach ($parameterTypeTypeParser->getTypeToParserCallableMap() as $type => $callable) {
            $this->parameterTypeParsers[$type] = $callable;
        }

        foreach ($parameterTypeTypeParser->getTypeToArgumentValidityCallableMap() as $type => $callable) {
            $this->parameterValidityCheckers[$type] = $callable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parseArgument(
        ParameterTypeInterface $parameterType,
        $rawValue
    ) {
        if (!array_key_exists($parameterType->getType(), $this->parameterTypeParsers)) {
            // No parser is registered for parameters of this type
            throw new InvalidArgumentException(sprintf(
                'Unknown parameter type "%s"',
                $parameterType->getType()
            ));
        }

        return $this->parameterTypeParsers[$parameterType->getType()](
            $parameterType,
            $rawValue
        );
    }

    /**
     * {@inheritdoc}
     */
    public function argumentIsValid(
        ParameterTypeInterface $parameterType,
        $value
    ) {
        if (!array_key_exists($parameterType->getType(), $this->parameterValidityCheckers)) {
            // No validator is registered for parameter types of this type
            throw new InvalidArgumentException(sprintf(
                'Unknown parameter type type "%s"',
                $parameterType->getType()
            ));
        }

        return $this->parameterValidityCheckers[$parameterType->getType()](
            $parameterType,
            $value
        );
    }
}
