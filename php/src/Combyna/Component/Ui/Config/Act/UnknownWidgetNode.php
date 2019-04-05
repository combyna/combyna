<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\DynamicUnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class UnknownWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'unknown-widget';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     */
    public function __construct($contextDescription)
    {
        $this->contextDescription = $contextDescription;
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
    public function getCaptureExpressionBag(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownExpressionBagNode(
            sprintf('%s capture set', $this->contextDescription),
            $queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownFixedStaticBagModelNode(
            sprintf('%s capture model', $this->contextDescription),
            $queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return 'unknown';
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return []; // Unknown widgets define no tags
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibilityExpression()
    {
        return new UnknownExpressionNode(sprintf('%s visibility expression', $this->contextDescription));
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'unknown';
    }
}
