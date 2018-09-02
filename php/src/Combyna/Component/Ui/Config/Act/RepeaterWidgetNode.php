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
use Combyna\Component\Behaviour\Spec\SubBehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Expression\Validation\Context\Specifier\ScopeContextSpecifier;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\ListElementTypeDeterminer;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class RepeaterWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'repeater';

    /**
     * @var string|null
     */
    private $indexVariableName;

    /**
     * @var ExpressionNodeInterface
     */
    private $itemListExpressionNode;

    /**
     * @var string
     */
    private $itemVariableName;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var WidgetNodeInterface
     */
    private $repeatedWidgetNode;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param ExpressionNodeInterface $itemListExpressionNode
     * @param string|null $indexVariableName
     * @param string $itemVariableName
     * @param WidgetNodeInterface $repeatedWidgetNode
     * @param string|null $name
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        ExpressionNodeInterface $itemListExpressionNode,
        $indexVariableName,
        $itemVariableName,
        WidgetNodeInterface $repeatedWidgetNode,
        $name,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->indexVariableName = $indexVariableName;
        $this->itemListExpressionNode = $itemListExpressionNode;
        $this->itemVariableName = $itemVariableName;
        $this->name = $name;
        $this->repeatedWidgetNode = $repeatedWidgetNode;
        $this->tags = $tags;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->itemListExpressionNode);
        // Make sure the item list expression evaluates to a list so that we can iterate over it
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->itemListExpressionNode,
                new PresolvedTypeDeterminer(new StaticListType(new AnyType())),
                'item list'
            )
        );

        if ($this->indexVariableName === '') {
            $specBuilder->addConstraint(
                new KnownFailureConstraint(
                    'Empty index variable name given - did you mean to leave it as null?'
                )
            );
        }

        if ($this->itemVariableName === '') {
            $specBuilder->addConstraint(new KnownFailureConstraint('Empty item variable name given'));
        }

        // Validate the repeated widget in a sub-context that has access to the item and/or index vars
        $specBuilder->addSubSpec(function (SubBehaviourSpecBuilderInterface $subSpecBuilder) {
            $scopeContextSpecifier = new ScopeContextSpecifier();
            $scopeContextSpecifier->defineVariable(
                $this->itemVariableName,
                new ListElementTypeDeterminer($this->itemListExpressionNode->getResultTypeDeterminer())
            );

            if ($this->indexVariableName !== null) {
                $scopeContextSpecifier->defineVariable(
                    $this->indexVariableName,
                    new PresolvedTypeDeterminer(new StaticType(NumberExpression::class))
                );
            }

            $subSpecBuilder->defineValidationContext($scopeContextSpecifier);

            $subSpecBuilder->addChildNode($this->repeatedWidgetNode);
        });
    }

    /**
     * Fetches the name to use for the variable that contains the index of the current item, if specified
     *
     * @return string|null
     */
    public function getIndexVariableName()
    {
        return $this->indexVariableName;
    }

    /**
     * Fetches the expression to be evaluated to get the list of items to iterate over
     *
     * @return ExpressionNodeInterface
     */
    public function getItemListExpression()
    {
        return $this->itemListExpressionNode;
    }

    /**
     * Fetches the name to use for the variable that contains the value of the current item
     *
     * @return string
     */
    public function getItemVariableName()
    {
        return $this->itemVariableName;
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
     * Fetches the widget to be repeated for each item in the list
     *
     * @return WidgetNodeInterface
     */
    public function getRepeatedWidget()
    {
        return $this->repeatedWidgetNode;
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
        return 'repeater';
    }
}
