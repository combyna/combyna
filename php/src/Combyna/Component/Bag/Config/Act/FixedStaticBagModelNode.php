<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class FixedStaticBagModelNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelNode extends AbstractActNode implements FixedStaticBagModelNodeInterface
{
    const TYPE = 'fixed-static-bag-model';

    /**
     * @var FixedStaticDefinitionNodeInterface[]
     */
    private $staticDefinitionNodes = [];

    /**
     * @param FixedStaticDefinitionNodeInterface[] $staticDefinitionNodes
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
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            $specBuilder->addChildNode($staticDefinitionNode);
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
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $determinedStaticDefinitionNodes = [];

        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            $determinedStaticDefinitionNodes[] = $staticDefinitionNode->determine($validationContext);
        }

        $determinedModelNode = new DeterminedFixedStaticBagModelNode($determinedStaticDefinitionNodes);

        return $determinedModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionByName($definitionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        if (array_key_exists($definitionName, $this->staticDefinitionNodes)) {
            return $this->staticDefinitionNodes[$definitionName];
        }

        return new UnknownFixedStaticDefinitionNode($definitionName, $dynamicActNodeAdopter);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionNames()
    {
        return array_keys($this->staticDefinitionNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitions()
    {
        return $this->staticDefinitionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validateStaticExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode,
        $contextDescription
    ) {
        // Check there are no required statics that are missing an expression
        foreach ($this->staticDefinitionNodes as $staticName => $definitionNode) {
            if (!$expressionBagNode->hasExpression($staticName) && $definitionNode->isRequired()) {
                $validationContext->addGenericViolation(sprintf(
                    $contextDescription . ' is missing an expression for attribute "%s"',
                    $staticName
                ));
            }
        }

        // Check there are no expressions that aren't needed/are extra
        foreach ($expressionBagNode->getExpressionNames() as $staticName) {
            if (!$this->definesStatic($staticName)) {
                $validationContext->addGenericViolation(sprintf(
                    $contextDescription . ' has an unnecessary extra expression for undefined attribute "%s"',
                    $staticName
                ));
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
