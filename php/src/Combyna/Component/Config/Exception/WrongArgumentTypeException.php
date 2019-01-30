<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Exception;

use Combyna\Component\Config\Parameter\Type\ParameterTypeInterface;
use Exception;

/**
 * Class WrongArgumentTypeException
 *
 * Thrown when an argument is specified for a parameter but has the wrong type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WrongArgumentTypeException extends Exception implements ArgumentMismatchExceptionInterface
{
    /**
     * @param string $parameterName
     * @param ParameterTypeInterface $expectedType
     * @param mixed $invalidValue
     */
    public function __construct($parameterName, ParameterTypeInterface $expectedType, $invalidValue)
    {
        $invalidValueSummary = is_object($invalidValue) ?
            get_class($invalidValue) :
            var_export($invalidValue, true);

        parent::__construct(sprintf(
            'Wrong type of value given for argument "%s": expected %s, got %s(%s)',
            $parameterName,
            $expectedType->getSummary(),
            gettype($invalidValue),
            $invalidValueSummary
        ));
    }
}
