<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Exception;

use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Exception;

/**
 * Class ValidationFailureException
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidationFailureException extends Exception
{
    /**
     * @var RootValidationContextInterface
     */
    private $rootValidationContext;

    /**
     * @param RootValidationContextInterface $rootValidationContext
     * @param string $descriptions
     */
    public function __construct(RootValidationContextInterface $rootValidationContext, $descriptions)
    {
        parent::__construct($descriptions);

        $this->rootValidationContext = $rootValidationContext;
    }

    // ...
}
