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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;

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
     * @var string|null
     */
    private $name;

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
     * @param string|null $name
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        array $childWidgetNodes,
        $name = null,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->childWidgetNodes = $childWidgetNodes;
        $this->name = $name;
        $this->tags = $tags;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
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

        // Recursively validate any child widgets
        foreach ($this->childWidgetNodes as $childWidgetNode) {
            $specBuilder->addChildNode($childWidgetNode);
        }
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
        return 'group';
    }
}
