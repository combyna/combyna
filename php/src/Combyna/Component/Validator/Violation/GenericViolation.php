<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Violation;

use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Class GenericViolation
 *
 * Represents a validation failure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericViolation implements ViolationInterface
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ValidationContextInterface $validationContext
     * @param string $description
     */
    public function __construct(
        $description,
        ValidationContextInterface $validationContext
    ) {
        $this->description = $description;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->validationContext->getPath();
    }
}
