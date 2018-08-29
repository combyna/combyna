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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\SubBehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;
use Combyna\Component\Ui\Validation\Context\Specifier\ViewContextSpecifier;

/**
 * Class PageViewNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewNode extends AbstractActNode implements ViewNodeInterface
{
    const TYPE = 'page-view';

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
     * @var WidgetNodeInterface
     */
    private $rootWidgetNode;

    /**
     * @var ViewStoreNode
     */
    private $storeNode;

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
     * @param WidgetNodeInterface $rootWidgetNode
     * @param ViewStoreNode|null $storeNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     */
    public function __construct(
        $name,
        ExpressionNodeInterface $titleExpressionNode,
        $description,
        FixedStaticBagModelNode $attributeBagModelNode,
        WidgetNodeInterface $rootWidgetNode,
        ViewStoreNode $storeNode = null,
        ExpressionNodeInterface $visibilityExpressionNode = null
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->description = $description;
        $this->name = $name;
        $this->rootWidgetNode = $rootWidgetNode;
        $this->storeNode = $storeNode;
        $this->titleExpressionNode = $titleExpressionNode;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeBagModelNode);

        $specBuilder->addSubSpec(function (SubBehaviourSpecBuilderInterface $subSpecBuilder) {
            $subSpecBuilder->defineValidationContext(new ViewContextSpecifier());

            $subSpecBuilder->addChildNode($this->rootWidgetNode);

            if ($this->storeNode) {
                $subSpecBuilder->addChildNode($this->storeNode);
            }

            $subSpecBuilder->addChildNode($this->titleExpressionNode);

            // Make sure the title expression always evaluates to text
            $subSpecBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->titleExpressionNode,
                    new StaticType(TextExpression::class),
                    'title'
                )
            );

            if ($this->visibilityExpressionNode !== null) {
                $subSpecBuilder->addChildNode($this->visibilityExpressionNode);

                // Make sure the visibility expression always evaluates to a boolean
                $subSpecBuilder->addConstraint(
                    new ResultTypeConstraint(
                        $this->visibilityExpressionNode,
                        new StaticType(BooleanExpression::class),
                        'visibility'
                    )
                );
            }
        });
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the root widget for this view
     *
     * @return WidgetNodeInterface
     */
    public function getRootWidget()
    {
        return $this->rootWidgetNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->storeNode;
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
}
