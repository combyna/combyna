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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class FixedStaticBagModel
 *
 * Defines the statics and their types that a bag may store internally
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModel implements FixedStaticBagModelInterface
{
    /**
     * @var FixedStaticDefinition[]
     */
    private $staticDefinitions = [];

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param FixedStaticDefinition[] $staticDefinitions
     */
    public function __construct(ValidationFactoryInterface $validationFactory, array $staticDefinitions)
    {
        // Index definitions by name to simplify lookups
        foreach ($staticDefinitions as $staticDefinition) {
            $this->staticDefinitions[$staticDefinition->getName()] = $staticDefinition;
        }

        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStatic($name, StaticInterface $value)
    {
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStaticBag(StaticBagInterface $staticBag)
    {
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function definesStatic($name)
    {
        return array_key_exists($name, $this->staticDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function validateStaticExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode,
        $contextDescription
    ) {
        // First check that the expressions in the bag are valid within themselves
        $expressionBagNode->validate($validationContext);

        // Check there are no required statics that are missing an expression
        foreach ($this->staticDefinitions as $definition) {
            $staticName = $definition->getName();

            if (!$expressionBagNode->hasExpression($staticName) && $definition->isRequired()) {
                $validationContext->addGenericViolation(
                    $contextDescription . ' is missing an expression for ' . $staticName
                );
            }
        }

        // Check there are no expressions that aren't needed/are extra
        foreach ($expressionBagNode->getExpressionNames() as $staticName) {
            if (!$this->definesStatic($staticName)) {
                $validationContext->addGenericViolation(
                    $contextDescription . ' has an unnecessary extra expression for ' . $staticName
                );
            }
        }

        // Check all expressions in the bag can only ever evaluate to valid values
        // for their corresponding parameters
        foreach ($this->staticDefinitions as $definition) {
            $staticExpression = $expressionBagNode->getExpression($definition->getName());

            $definition->validateExpression(
                $staticExpression,
                $validationContext,
                $contextDescription
            );
        }
    }
}
