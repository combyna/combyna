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

use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;

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
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

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
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->name = $name;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);

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
    public function getCaptureExpressionBag()
    {
        return new UnknownExpressionBagNode(
            sprintf('%s capture set', $this->contextDescription),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf('%s capture model', $this->contextDescription),
            $this->dynamicContainerNode
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
        return new UnknownExpressionNode(
            sprintf('%s visibility expression', $this->contextDescription),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'unknown';
    }
}
