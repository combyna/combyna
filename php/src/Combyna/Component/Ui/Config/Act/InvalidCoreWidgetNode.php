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
 * Class InvalidCoreWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidCoreWidgetNode extends AbstractActNode implements CoreWidgetNodeInterface
{
    const TYPE = 'invalid-core-widget';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $widgetDefinitionName
     * @param string $name
     * @param string $contextDescription
     */
    public function __construct($widgetDefinitionName, $name, $contextDescription)
    {
        $this->contextDescription = $contextDescription;
        $this->name = $name;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Core "%s" widget "%s" is invalid: %s',
                    $this->widgetDefinitionName,
                    $this->name,
                    $this->contextDescription
                )
            )
        );
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
    public function getIdentifier()
    {
        return $this->getType() . ':' . $this->name;
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
        // TODO: Remove visibility expressions
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
