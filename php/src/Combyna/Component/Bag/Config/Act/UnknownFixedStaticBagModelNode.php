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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class UnknownFixedStaticBagModelNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFixedStaticBagModelNode extends AbstractActNode implements FixedStaticBagModelNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-fixed-static-bag-model';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var QueryRequirementInterface
     */
    private $queryRequirement;

    /**
     * @param string $contextDescription
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($contextDescription, QueryRequirementInterface $queryRequirement)
    {
        $this->contextDescription = $contextDescription;
        $this->queryRequirement = $queryRequirement;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(new KnownFailureConstraint($this->contextDescription));
    }

    /**
     * {@inheritdoc}
     */
    public function definesStatic($name)
    {
        return false; // Unknown static bag model cannot define any statics
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionByName($definitionName, QueryRequirementInterface $queryRequirement)
    {
        // Unknown static bag model cannot define any statics
        return new UnknownFixedStaticDefinitionNode($definitionName, $this->queryRequirement);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitions()
    {
        return []; // Unknown static bag model cannot define any statics
    }

    /**
     * {@inheritdoc}
     */
    public function validateStaticExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode,
        $contextDescription
    ) {
        // Nothing to do: the behaviour spec will make sure that validation fails
    }
}
