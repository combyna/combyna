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
use Combyna\Component\Ui\Validation\Constraint\CompoundWidgetDefinitionHasChildConstraint;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureDefinitionsSpecModifier;
use Combyna\Component\Ui\Validation\Constraint\ValidCaptureSetsSpecModifier;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class ChildReferenceWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetNode extends AbstractActNode implements CoreWidgetNodeInterface
{
    const TYPE = 'child-reference-widget';
    const WIDGET_TYPE = 'child';

    /**
     * @var ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

    /**
     * @var string
     */
    private $childName;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param string $childName
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     * @param ExpressionBagNode $captureExpressionBagNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        $childName,
        FixedStaticBagModelNodeInterface $captureStaticBagModelNode,
        ExpressionBagNode $captureExpressionBagNode,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->captureExpressionBagNode = $captureExpressionBagNode;
        $this->captureStaticBagModelNode = $captureStaticBagModelNode;
        $this->childName = $childName;
        $this->tags = $tags;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Check that the compound widget we are inside actually has a child with this name
        $specBuilder->addConstraint(new CompoundWidgetDefinitionHasChildConstraint($this->childName));

        // TODO: Remove captures support for this type of node?
        $specBuilder->addChildNode($this->captureExpressionBagNode);
        $specBuilder->addChildNode($this->captureStaticBagModelNode);
        $specBuilder->addModifier(new ValidCaptureDefinitionsSpecModifier($this->captureStaticBagModelNode));
        $specBuilder->addModifier(new ValidCaptureSetsSpecModifier($this->captureExpressionBagNode));

        if ($this->visibilityExpressionNode !== null) {
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
     * Fetches the name of the compound widget child to reference
     *
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
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
