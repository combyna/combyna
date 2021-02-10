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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use InvalidArgumentException;

/**
 * Class DelegatingParameterParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingParameterParser implements ParameterParserInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $parameterParsers = [];

    /**
     * @var callable[]
     */
    private $parameterValidators = [];

    /**
     * @param ParameterTypeParserInterface $parameterTypeParser
     */
    public function addParser(ParameterTypeParserInterface $parameterTypeParser)
    {
        foreach ($parameterTypeParser->getTypeToParserCallableMap() as $type => $callable) {
            $this->parameterParsers[$type] = $callable;
        }

        foreach ($parameterTypeParser->getTypeToValidatorCallableMap() as $type => $callable) {
            $this->parameterValidators[$type] = $callable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parseArgument(
        ParameterInterface $parameter,
        array $config,
        array $parameterList,
        array $extraArguments
    ) {
        if (!array_key_exists($parameter->getType(), $this->parameterParsers)) {
            // No parser is registered for parameters of this type
            throw new InvalidArgumentException(sprintf(
                'Unknown parameter type "%s"',
                $parameter->getType()
            ));
        }

        return $this->parameterParsers[$parameter->getType()](
            $parameter,
            $config,
            $parameterList,
            $extraArguments
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgument(
        ParameterInterface $parameter,
        array $config
    ) {
        if (!array_key_exists($parameter->getType(), $this->parameterValidators)) {
            // No validator is registered for parameters of this type
            throw new InvalidArgumentException(sprintf(
                'Unknown parameter type "%s"',
                $parameter->getType()
            ));
        }

        return $this->parameterValidators[$parameter->getType()](
            $parameter,
            $config
        );
    }
}
