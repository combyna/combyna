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
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureDefinitionsSpecModifier;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureSetsSpecModifier;
use Combyna\Component\Ui\Validation\Context\Specifier\ConditionalWidgetContextSpecifier;
use Combyna\Component\Validator\Type\StaticTypeDeterminer;

/**
 * Class ConditionalWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetNode extends AbstractActNode implements CoreWidgetNodeInterface, OptionalWidgetNodeInterface
{
    const TYPE = 'conditional';
    const WIDGET_TYPE = 'conditional';

    /**
     * @var WidgetNodeInterface|null
     */
    private $alternateWidgetNode;

    /**
     * @var ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

    /**
     * @var ExpressionNodeInterface
     */
    private $conditionExpressionNode;

    /**
     * @var WidgetNodeInterface
     */
    private $consequentWidgetNode;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var array
     */
    private $tags;

    /**
     * @param ExpressionNodeInterface $conditionExpressionNode
     * @param WidgetNodeInterface $consequentWidgetNode
     * @param WidgetNodeInterface|null $alternateWidgetNode
     * @param string|int $name
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     * @param ExpressionBagNode $captureExpressionBagNode
     * @param array $tags
     */
    public function __construct(
        ExpressionNodeInterface $conditionExpressionNode,
        WidgetNodeInterface $consequentWidgetNode,
        WidgetNodeInterface $alternateWidgetNode = null,
        $name,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        array $tags = []
    ) {
        $this->alternateWidgetNode = $alternateWidgetNode;
        $this->captureExpressionBagNode = $captureExpressionBagNode;
        $this->captureStaticBagModelNode = $captureStaticBagModelNode;
        $this->conditionExpressionNode = $conditionExpressionNode;
        $this->consequentWidgetNode = $consequentWidgetNode;
        $this->name = $name;
        $this->tags = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new ConditionalWidgetContextSpecifier());

        $specBuilder->addChildNode($this->conditionExpressionNode);
        // Make sure the condition evaluates to a boolean
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->conditionExpressionNode,
                new StaticTypeDeterminer(BooleanExpression::class),
                'condition'
            )
        );

        $specBuilder->addChildNode($this->captureExpressionBagNode);
        $specBuilder->addChildNode($this->captureStaticBagModelNode);
        $specBuilder->addModifier(new ValidCaptureDefinitionsSpecModifier($this->captureStaticBagModelNode));
        $specBuilder->addModifier(new ValidCaptureSetsSpecModifier($this->captureExpressionBagNode));

        $specBuilder->addChildNode($this->consequentWidgetNode);

        if ($this->alternateWidgetNode !== null) {
            // Alternate node doesn't have to be specified (if it isn't,
            // then this widget will render as nothing when the condition evaluates to false)
            $specBuilder->addChildNode($this->alternateWidgetNode);
        }
    }

    /**
     * Fetches the widget to be shown when the condition evaluates to false, if any
     *
     * @return WidgetNodeInterface|null
     */
    public function getAlternateWidget()
    {
        return $this->alternateWidgetNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureExpressionBag()
    {
        return $this->captureExpressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel()
    {
        return $this->captureStaticBagModelNode;
    }

    /**
     * Fetches the expression to be evaluated to determine whether to show the consequent widget
     *
     * @return ExpressionNodeInterface
     */
    public function getConditionExpression()
    {
        return $this->conditionExpressionNode;
    }

    /**
     * Fetches the widget to be shown when the condition evaluates to true
     *
     * @return WidgetNodeInterface
     */
    public function getConsequentWidget()
    {
        return $this->consequentWidgetNode;
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
        return null; // TODO - remove visibility exprs in favour of these ConditionalWidgets
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return self::WIDGET_TYPE;
    }
}
