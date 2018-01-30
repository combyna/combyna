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
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

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
     * @param WidgetNodeInterface[] $childWidgetNodes
     * @param TriggerNode[] $triggerNodes
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        $widgetDefinitionLibraryName,
        $widgetDefinitionName,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes,
        array $triggerNodes,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags
    ) {
        $this->attributeExpressionBagNode = $attributeExpressionBagNode;
        $this->childWidgetNodes = $childWidgetNodes;
        $this->tags = $tags;
        $this->triggerNodes = $triggerNodes;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
        $this->widgetDefinitionLibraryName = $widgetDefinitionLibraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
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
     * {@inheritdoc}
     */
    public function getChildWidgets()
    {
        return $this->childWidgetNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->widgetDefinitionLibraryName;
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

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeExpressionBagNode->validate($subValidationContext);

        // Validate ourself
        if ($this->visibilityExpressionNode) {
            $this->visibilityExpressionNode->validate($subValidationContext);

            $subValidationContext->assertResultType(
                $this->visibilityExpressionNode,
                new StaticType(BooleanExpression::class),
                'visibility'
            );
        }

        // FIXME: Reinstate this validation somehow
//        $this->widgetDefinitionNode->validateWidget(
//            $subValidationContext,
//            $this->attributeExpressionBagNode,
//            $this->childWidgetNodes
//        );

        // Validate all triggers for this widget
        foreach ($this->triggerNodes as $triggerNode) {
            $triggerNode->validate($subValidationContext);
        }

        // Recursively validate any child widgets
        foreach ($this->childWidgetNodes as $childWidgetNode) {
            $childWidgetNode->validate($subValidationContext);
        }
    }
}
