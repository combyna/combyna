<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression\Validation\Exception;

use Combyna\Expression\Validation\ValidationContextInterface;
use Exception;

/**
 * Class ValidationFailureException
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidationFailureException extends Exception
{
    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ValidationContextInterface $validationContext
     * @param string $descriptions
     */
    public function __construct(ValidationContextInterface $validationContext, $descriptions)
    {
        parent::__construct($descriptions);

        $this->validationContext = $validationContext;
    }

    // ...
}
