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

use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\ViolationInterface;

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
     * @var SubValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param SubValidationContextInterface $subValidationContext
     * @param string $contextDescription
     */
    public function __construct(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        SubValidationContextInterface $subValidationContext,
        $contextDescription
    ) {
        $this->actualType = $actualType;
        $this->contextDescription = $contextDescription;
        $this->expectedType = $expectedType;
        $this->subValidationContext = $subValidationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $actualType = $this->actualType;
        $expectedType = $this->expectedType;

        // Only display value information for both sides if the expected type has some available
        if ($expectedType->hasValue()) {
            $actualTypeSummary = $actualType->getSummaryWithValue();
            $expectedTypeSummary = $expectedType->getSummaryWithValue();
        } else {
            $actualTypeSummary = $actualType->getSummary();
            $expectedTypeSummary = $expectedType->getSummary();
        }

        return $this->contextDescription . ' would get [' .
            $actualTypeSummary .
            '], expects [' .
            $expectedTypeSummary .
            ']';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->subValidationContext->getPath();
    }
}
