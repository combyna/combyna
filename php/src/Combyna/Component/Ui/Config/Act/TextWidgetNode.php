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
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureDefinitionsSpecModifier;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureSetsSpecModifier;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class TextWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidgetNode extends AbstractActNode implements CoreWidgetNodeInterface
{
    const TYPE = 'text-widget';
    const WIDGET_TYPE = 'text';

    /**
     * @var ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface
     */
    private $textExpressionNode;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param ExpressionNodeInterface $textExpressionNode
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     * @param ExpressionBagNode $captureExpressionBagNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        ExpressionNodeInterface $textExpressionNode,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->captureExpressionBagNode = $captureExpressionBagNode;
        $this->captureStaticBagModelNode = $captureStaticBagModelNode;
        $this->tags = $tags;
        $this->textExpressionNode = $textExpressionNode;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
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
     * Fetches the expression to be evaluated for the text content of this widget
     *
     * @return ExpressionNodeInterface
     */
    public function getTextExpression()
    {
        return $this->textExpressionNode;
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

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->textExpressionNode);

        $specBuilder->addChildNode($this->captureExpressionBagNode);
        $specBuilder->addChildNode($this->captureStaticBagModelNode);
        $specBuilder->addModifier(new ValidCaptureDefinitionsSpecModifier($this->captureStaticBagModelNode));
        $specBuilder->addModifier(new ValidCaptureSetsSpecModifier($this->captureExpressionBagNode));

        // Make sure the text expression always evaluates to some text
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->textExpressionNode,
                new PresolvedTypeDeterminer(new StaticType(TextExpression::class)),
                'text'
            )
        );

        if ($this->visibilityExpressionNode) {
            $specBuilder->addChildNode($this->visibilityExpressionNode);

            // Make sure the visibility expression always evaluates to a boolean
            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->visibilityExpressionNode,
                    new PresolvedTypeDeterminer(new StaticType(BooleanExpression::class)),
                    'visibility'
                )
            );
        }
    }
}
