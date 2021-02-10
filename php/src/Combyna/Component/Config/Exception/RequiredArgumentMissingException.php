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

use Exception;

/**
 * Class RequiredArgumentMissingException
 *
 * Thrown when a required argument is not specified
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RequiredArgumentMissingException extends Exception implements ArgumentMismatchExceptionInterface
{
    /**
     * @param string $parameterName
     * @param array $argumentNames
     */
    public function __construct($parameterName, array $argumentNames)
    {
        parent::__construct(sprintf(
            'Required argument "%s" missing, got: [%s]',
            $parameterName,
            implode(', ', $argumentNames)
        ));
    }
}
