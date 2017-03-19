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

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class ViewNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewNode extends AbstractActNode
{
    const TYPE = 'view';

    /**
     * @var FixedStaticBagModelNode
     */
    private $attributeBagModelNode;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $name;

    /**
     * @var WidgetNode
     */
    private $rootWidgetNode;

    /**
     * @var ExpressionNodeInterface
     */
    private $titleExpressionNode;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param string $name
     * @param ExpressionNodeInterface $titleExpressionNode
     * @param string $description
     * @param FixedStaticBagModelNode $attributeBagModelNode
     * @param WidgetNode $rootWidgetNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     */
    public function __construct(
        $name,
        ExpressionNodeInterface $titleExpressionNode,
        $description,
        FixedStaticBagModelNode $attributeBagModelNode,
        WidgetNode $rootWidgetNode,
        ExpressionNodeInterface $visibilityExpressionNode = null
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->description = $description;
        $this->name = $name;
        $this->rootWidgetNode = $rootWidgetNode;
        $this->titleExpressionNode = $titleExpressionNode;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * Fetches the model for attributes of this view
     *
     * @return FixedStaticBagModelNode
     */
    public function getAttributeBagModel()
    {
        return $this->attributeBagModelNode;
    }

    /**
     * Fetches the description of this view
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Fetches the unique name of this view
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the root widget for this view
     *
     * @return WidgetNode
     */
    public function getRootWidget()
    {
        return $this->rootWidgetNode;
    }

    /**
     * Fetches the expression to evaluate to generate the title for this view
     *
     * @return ExpressionNodeInterface
     */
    public function getTitleExpression()
    {
        return $this->titleExpressionNode;
    }

    /**
     * Fetches the expression to determine whether this view is visible, if set
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
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->titleExpressionNode->validate($subValidationContext);

        if ($this->visibilityExpressionNode !== null) {
            $this->visibilityExpressionNode->validate($subValidationContext);

            $subValidationContext->assertResultType(
                $this->visibilityExpressionNode,
                new StaticType(BooleanExpression::class),
                'visibility'
            );
        }

        $this->rootWidgetNode->validate($subValidationContext);
    }
}
