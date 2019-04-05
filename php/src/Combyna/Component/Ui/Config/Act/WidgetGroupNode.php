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
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureDefinitionsSpecModifier;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureSetsSpecModifier;
use Combyna\Component\Ui\Validation\Context\Specifier\WidgetGroupContextSpecifier;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class WidgetGroupNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupNode extends AbstractActNode implements CoreWidgetNodeInterface
{
    const TYPE = 'widget-group';
    const WIDGET_TYPE = 'group';

    /**
     * @var ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

    /**
     * @var WidgetNodeInterface[]
     */
    private $childWidgetNodes;

    /**
     * @var string|int
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
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     * @param ExpressionBagNode $captureExpressionBagNode
     * @param string|int $name
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        array $childWidgetNodes,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        $name,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->captureExpressionBagNode = $captureExpressionBagNode;
        $this->captureStaticBagModelNode = $captureStaticBagModelNode;
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
        $specBuilder->defineValidationContext(new WidgetGroupContextSpecifier());

        $specBuilder->addChildNode($this->captureExpressionBagNode);
        $specBuilder->addChildNode($this->captureStaticBagModelNode);
        $specBuilder->addModifier(new ValidCaptureDefinitionsSpecModifier($this->captureStaticBagModelNode));
        $specBuilder->addModifier(new ValidCaptureSetsSpecModifier($this->captureExpressionBagNode));

        if ($this->visibilityExpressionNode) {
            $specBuilder->addChildNode($this->visibilityExpressionNode);

            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->visibilityExpressionNode,
                    new PresolvedTypeDeterminer(new StaticType(BooleanExpression::class)),
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
     * {@inheritdoc}
     */
    public function getCaptureExpressionBag(QueryRequirementInterface $queryRequirement)
    {
        return $this->captureExpressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel(QueryRequirementInterface $queryRequirement)
    {
        return $this->captureStaticBagModelNode;
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
        return self::WIDGET_TYPE;
    }
}
