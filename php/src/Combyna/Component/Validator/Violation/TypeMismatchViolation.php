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
use Combyna\Component\Type\TypeInterface;

/**
 * Class TypeMismatchViolation
 *
 * Represents a validation failure where the eventual static type an expression evaluates to
 * does not match the specification defined by a type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeMismatchViolation implements ViolationInterface
{
    /**
     * @var TypeInterface
     */
    private $actualType;

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var TypeInterface
     */
    private $expectedType;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param ValidationContextInterface $validationContext
     * @param string $contextDescription
     */
    public function __construct(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        $this->actualType = $actualType;
        $this->contextDescription = $contextDescription;
        $this->expectedType = $expectedType;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->contextDescription . ' would get [' .
            $this->actualType->getSummary() .
            '], expects [' .
            $this->expectedType->getSummary() .
            ']';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->validationContext->getPath();
    }
}
