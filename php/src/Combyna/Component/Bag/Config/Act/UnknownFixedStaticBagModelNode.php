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
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class UnknownFixedStaticBagModelNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownFixedStaticBagModelNode extends AbstractActNode implements FixedStaticBagModelNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-fixed-static-bag-model';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($contextDescription, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->contextDescription = $contextDescription;
        $this->dynamicContainerNode = new DynamicContainerNode();

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);

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
    public function determine(ValidationContextInterface $validationContext)
    {
        throw new LogicException('Cannot determine an unknown fixed static bag model node');
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionByName($definitionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        // Unknown static bag model cannot define any statics
        return new UnknownFixedStaticDefinitionNode(
            $definitionName,
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionNames()
    {
        return []; // Unknown static bag model cannot define any statics
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
