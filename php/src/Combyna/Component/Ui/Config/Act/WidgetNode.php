<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class WidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetNode extends AbstractActNode
{
    const TYPE = 'widget';

    /**
     * @var ExpressionBagNode
     */
    private $attributeExpressionBagNode;

    /**
     * @var WidgetNode[]
     */
    private $childWidgetNodes;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @var WidgetDefinitionNodeInterface
     */
    private $widgetDefinitionNode;

    /**
     * @param WidgetDefinitionNodeInterface $widgetDefinitionNode
     * @param ExpressionBagNode $attributeExpressionBagNode
     * @param WidgetNode[] $childWidgetNodes
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     */
    public function __construct(
        WidgetDefinitionNodeInterface $widgetDefinitionNode,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes,
        ExpressionNodeInterface $visibilityExpressionNode = null
    ) {
        $this->attributeExpressionBagNode = $attributeExpressionBagNode;
        $this->childWidgetNodes = $childWidgetNodes;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
        $this->widgetDefinitionNode = $widgetDefinitionNode;
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
     * @return WidgetNode[]
     */
    public function getChildWidgets()
    {
        return $this->childWidgetNodes;
    }

    /**
     * Fetches the name of the library this widget's definition should be fetched from
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->widgetDefinitionNode->getLibraryName();
    }

    /**
     * Fetches the expression used to determine whether this widget is visible, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getVisibilityExpression()
    {
        return $this->visibilityExpressionNode;
    }

    /**
     * Fetches the name of the definition for this widget
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionNode->getWidgetDefinitionName();
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

        $this->widgetDefinitionNode->validateWidget(
            $subValidationContext,
            $this->attributeExpressionBagNode,
            $this->childWidgetNodes
        );

        // Recursively validate any child widgets
        foreach ($this->childWidgetNodes as $childWidgetNode) {
            $childWidgetNode->validate($subValidationContext);
        }
    }
}
