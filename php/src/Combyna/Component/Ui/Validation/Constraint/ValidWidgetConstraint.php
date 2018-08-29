<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class ValidWidgetConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidWidgetConstraint implements ConstraintInterface
{
    /**
     * @var ExpressionBagNode
     */
    private $attributeExpressionBagNode;

    /**
     * @var WidgetNodeInterface[]
     */
    private $childWidgetNodes;

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
     */
    public function __construct(
        $widgetDefinitionLibraryName,
        $widgetDefinitionName,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        $this->attributeExpressionBagNode = $attributeExpressionBagNode;
        $this->childWidgetNodes = $childWidgetNodes;
        $this->widgetDefinitionLibraryName = $widgetDefinitionLibraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * Fetches the bag of expressions used to evaluate the attributes for the widget
     *
     * @return ExpressionBagNode
     */
    public function getAttributeExpressionBag()
    {
        return $this->attributeExpressionBagNode;
    }

    /**
     * Fetches the child widget nodes for the widget
     *
     * @return WidgetNodeInterface[]
     */
    public function getChildWidgets()
    {
        return $this->childWidgetNodes;
    }

    /**
     * Fetches the name of the library that should define the widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->widgetDefinitionLibraryName;
    }

    /**
     * Fetches the name of the widget definition, unique within its library
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }
}
