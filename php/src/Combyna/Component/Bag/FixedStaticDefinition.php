<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;
use Combyna\Component\Type\TypeInterface;
use LogicException;

/**
 * Class FixedStaticDefinition
 *
 * Defines the name, type and default static value for a static in a bag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticDefinition
{
    /**
     * @var StaticInterface|null
     */
    private $defaultStatic;

    /**
     * @var string
     */
    private $name;

    /**
     * @var TypeInterface
     */
    private $staticType;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param string $name
     * @param TypeInterface $staticType
     * @param StaticInterface|null $defaultStatic
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        $name,
        TypeInterface $staticType,
        StaticInterface $defaultStatic = null
    ) {
        $this->defaultStatic = $defaultStatic;
        $this->name = $name;
        $this->staticType = $staticType;
        $this->validationFactory = $validationFactory;
    }

    /**
     * Fetches the default value for this static, if configured
     *
     * @return StaticInterface
     * @throws LogicException when no default static has been configured
     */
    public function getDefaultStatic()
    {
        if (!$this->defaultStatic) {
            throw new LogicException(
                'No default static has been configured for parameter "' . $this->name . '"'
            );
        }

        return $this->defaultStatic;
    }

    /**
     * Fetches the name of the definition
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Determines whether this static must be defined in the bag or not
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->defaultStatic === null;
    }

    /**
     * Checks that the provided expression evaluates to a static
     * that is compatible with this definition's type
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param ValidationContextInterface $validationContext
     * @param string $contextDescription
     */
    public function validateExpression(
        ExpressionNodeInterface $expressionNode,
        ValidationContextInterface $validationContext,
        $contextDescription
    ) {
        if (!$this->staticType->allows($expressionNode->getResultType($validationContext))) {
            $validationContext->addViolation(
                $this->validationFactory->createTypeMismatchViolation(
                    $this->staticType,
                    $expressionNode->getResultType($validationContext),
                    $validationContext,
                    $contextDescription . ' ' . $this->name
                )
            );
        }
    }
}
