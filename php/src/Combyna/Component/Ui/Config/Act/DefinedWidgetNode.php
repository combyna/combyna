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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Validation\Constraint\ValidWidgetConstraint;

/**
 * Class DefinedWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'defined-widget';

    /**
     * @var ExpressionBagNode
     */
    private $attributeExpressionBagNode;

    /**
     * @var WidgetNodeInterface[]
     */
    private $childWidgetNodes;

    /**
     * The name of this widget, if set, unique amongst its siblings
     *
     * @var string|null
     */
    private $name;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var TriggerNode[]
     */
    private $triggerNodes;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @var string
     */
    private $widgetDefinitionLibraryName;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $widgetDefinitionLibraryName
     * @param string $widgetDefinitionName
     * @param ExpressionBagNode $attributeExpressionBagNode
     * @param string|null $name
     * @param WidgetNodeInterface[] $childWidgetNodes
     * @param TriggerNode[] $triggerNodes
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        $widgetDefinitionLibraryName,
        $widgetDefinitionName,
        ExpressionBagNode $attributeExpressionBagNode,
        $name = null,
        array $childWidgetNodes = [],
        array $triggerNodes = [],
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->attributeExpressionBagNode = $attributeExpressionBagNode;
        $this->childWidgetNodes = $childWidgetNodes;
        $this->name = $name;
        $this->tags = $tags;
        $this->triggerNodes = $triggerNodes;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
        $this->widgetDefinitionLibraryName = $widgetDefinitionLibraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeExpressionBagNode);

        // Validate all triggers for this widget
        foreach ($this->triggerNodes as $triggerNode) {
            $specBuilder->addChildNode($triggerNode);
        }

        // Recursively validate any child widgets
        foreach ($this->childWidgetNodes as $childWidgetNode) {
            $specBuilder->addChildNode($childWidgetNode);
        }

        // Validate the visibility expression, if specified
        if ($this->visibilityExpressionNode) {
            $specBuilder->addChildNode($this->visibilityExpressionNode);

            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->visibilityExpressionNode,
                    new StaticType(BooleanExpression::class),
                    'visibility'
                )
            );
        }

        // Validate that this widget is a well-formed instance of its definition
        $specBuilder->addConstraint(
            new ValidWidgetConstraint(
                $this->widgetDefinitionLibraryName,
                $this->widgetDefinitionName,
                $this->attributeExpressionBagNode,
                $this->childWidgetNodes
            )
        );
    }

    /**
     * Fetches the expression bag used to determine the attributes for the widget's definition
     *
     * @return ExpressionBagNode
     */
    public function getAttributeExpressionBag()
    {
        return $this->attributeExpressionBagNode;
    }

    /**
     * Fetches the children of this widget, if any have been added
     *
     * @return WidgetNodeInterface[]
     */
    public function getChildWidgets()
    {
        return $this->childWidgetNodes;
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
        return $this->widgetDefinitionLibraryName;
    }

    /**
     * Returns the name of this widget, if set, unique amongst its siblings
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Fetches the triggers for this widget, which listen for events and dispatch signals in response
     *
     * @return TriggerNode[]
     */
    public function getTriggers()
    {
        return $this->triggerNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibilityExpression()
    {
        return $this->visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }
}
