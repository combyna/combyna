<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class FixedStaticBagModelNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelNode extends AbstractActNode
{
    const TYPE = 'fixed-static-bag-model';

    /**
     * @var FixedStaticDefinitionNode[]
     */
    private $staticDefinitionNodes = [];

    /**
     * @param FixedStaticDefinitionNode[] $staticDefinitionNodes
     */
    public function __construct(array $staticDefinitionNodes)
    {
        // Index definitions by name to simplify lookups
        foreach ($staticDefinitionNodes as $staticDefinitionNode) {
            $this->staticDefinitionNodes[$staticDefinitionNode->getName()] = $staticDefinitionNode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function definesStatic($name)
    {
        return array_key_exists($name, $this->staticDefinitionNodes);
    }

    /**
     * Fetches the definitions for statics in bags of this model
     *
     * @return FixedStaticDefinitionNode[]
     */
    public function getStaticDefinitions()
    {
        return $this->staticDefinitionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            $staticDefinitionNode->validate($subValidationContext);
        }
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
        foreach ($this->staticDefinitionNodes as $staticName => $definitionNode) {
            if (!$expressionBagNode->hasExpression($staticName) && $definitionNode->isRequired()) {
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
        foreach ($this->staticDefinitionNodes as $definitionNode) {
            if (!$expressionBagNode->hasExpression($definitionNode->getName())) {
                // Skip any undefined expressions as we won't be able to fetch them.
                // Validation should already have been marked failed above
                continue;
            }

            $staticExpressionNode = $expressionBagNode->getExpression($definitionNode->getName());

            $definitionNode->validateExpression(
                $staticExpressionNode,
                $validationContext,
                $contextDescription
            );
        }
    }
}
