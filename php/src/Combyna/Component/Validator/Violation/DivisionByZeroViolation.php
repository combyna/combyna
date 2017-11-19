<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Violation;

use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Class DivisionByZeroViolation
 *
 * Represents a validation failure where the eventual static value an expression evaluates to
 * is guaranteed to be zero, when it is to be used as the divisor in a division operation
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DivisionByZeroViolation implements ViolationInterface
{
    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        ValidationContextInterface $validationContext
    ) {
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Division by zero';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->validationContext->getPath();
    }
}
