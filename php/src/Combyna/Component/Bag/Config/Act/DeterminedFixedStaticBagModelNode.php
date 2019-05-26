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

use BadMethodCallException;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class DeterminedFixedStaticBagModelNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DeterminedFixedStaticBagModelNode extends AbstractActNode implements DeterminedFixedStaticBagModelInterface, FixedStaticBagModelNodeInterface
{
    const TYPE = 'determined-fixed-static-bag-model';

    /**
     * @var DeterminedFixedStaticDefinitionNode[]
     */
    private $staticDefinitionNodes = [];

    /**
     * @param DeterminedFixedStaticDefinitionNode[] $staticDefinitionNodes
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
    public function allowsOtherModel(DeterminedFixedStaticBagModelInterface $otherModel)
    {
        // Check there are no required statics in this model that are missing from the other one
        foreach ($this->staticDefinitionNodes as $definitionName => $definitionNode) {
            if (!$otherModel->definesStatic($definitionName) && $definitionNode->isRequired()) {
                return false;
            }
        }

        // Check there are no statics in the other model that aren't part of this one
        foreach ($otherModel->getStaticDefinitionNames() as $definitionName) {
            if (!$this->definesStatic($definitionName)) {
                return false;
            }
        }

        // Check all statics in the other model are allowed
        // by their corresponding static definitions in this one
        foreach ($otherModel->getStaticDefinitions() as $theirDefinitionNode) {
            $ourDefinitionNode = $this->staticDefinitionNodes[$theirDefinitionNode->getName()];

            if (!$ourDefinitionNode->allowsStaticDefinition($theirDefinitionNode)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticBag(StaticBagInterface $staticBag)
    {
        throw new BadMethodCallException(__METHOD__ . ' :: Not implemented');
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
        return $this;
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
    public function getSummary()
    {
        $staticDefinitionSummaries = [];

        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            $staticDefinitionSummaries[] = sprintf(
                '%s: %s',
                $staticDefinitionNode->getName(),
                $staticDefinitionNode->getStaticTypeSummary()
            );
        }

        return sprintf('{%s}', implode(', ', $staticDefinitionSummaries));
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        $staticDefinitionSummaries = [];

        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            $staticDefinitionSummaries[] = sprintf(
                '%s: %s',
                $staticDefinitionNode->getName(),
                $staticDefinitionNode->getStaticTypeSummaryWithValue() // As above, but including value info
            );
        }

        return sprintf('{%s}', implode(', ', $staticDefinitionSummaries));
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        foreach ($this->staticDefinitionNodes as $staticDefinitionNode) {
            if ($staticDefinitionNode->staticTypeHasValue()) {
                // If the type of any definition in the model contains value information,
                // treat the whole model as having it so it can be displayed if needed
                return true;
            }
        }

        return false;
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
                    $contextDescription . ' is missing an expression for static "%s"',
                    $staticName
                ));
            }
        }

        // Check there are no expressions that aren't needed/are extra
        foreach ($expressionBagNode->getExpressionNames() as $staticName) {
            if (!$this->definesStatic($staticName)) {
                $validationContext->addGenericViolation(sprintf(
                    $contextDescription . ' has an unnecessary extra expression for undefined static "%s"',
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
