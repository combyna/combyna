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

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class WidgetGroupNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'widget-group';

    /**
     * @var WidgetNodeInterface[]
     */
    private $childWidgetNodes;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param WidgetNodeInterface[] $childWidgetNodes
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        array $childWidgetNodes,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->childWidgetNodes = $childWidgetNodes;
        $this->tags = $tags;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
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
    public function getLibraryName()
    {
        return LibraryInterface::CORE;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
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
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        // Validate ourself
        if ($this->visibilityExpressionNode) {
            $this->visibilityExpressionNode->validate($subValidationContext);

            $subValidationContext->assertResultType(
                $this->visibilityExpressionNode,
                new StaticType(BooleanExpression::class),
                'visibility'
            );
        }

        // Recursively validate any child widgets
        foreach ($this->childWidgetNodes as $childWidgetNode) {
            $childWidgetNode->validate($subValidationContext);
        }
    }
}
