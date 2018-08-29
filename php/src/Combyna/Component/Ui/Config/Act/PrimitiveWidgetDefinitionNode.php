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
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class PrimitiveWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinitionNode extends AbstractActNode implements WidgetDefinitionNodeInterface
{
    const TYPE = PrimitiveWidgetDefinition::TYPE;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $attributeBagModelNode;

    /**
     * @var ChildWidgetDefinitionNodeInterface[]
     */
    private $childDefinitionNodes;

    /**
     * @var EventDefinitionReferenceNode[]
     */
    private $eventDefinitionReferenceNodes;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param FixedStaticBagModelNodeInterface $attributeBagModelNode
     * @param ChildWidgetDefinitionNodeInterface[] $childDefinitionNodes
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        FixedStaticBagModelNodeInterface $attributeBagModelNode,
        array $childDefinitionNodes,
        array $eventDefinitionReferenceNodes
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->childDefinitionNodes = $childDefinitionNodes;
        $this->eventDefinitionReferenceNodes = $eventDefinitionReferenceNodes;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeBagModelNode);

        foreach ($this->childDefinitionNodes as $childDefinitionNode) {
            $specBuilder->addChildNode($childDefinitionNode);
        }

        foreach ($this->eventDefinitionReferenceNodes as $eventDefinitionReferenceNode) {
            $specBuilder->addChildNode($eventDefinitionReferenceNode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBagModel()
    {
        return $this->attributeBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildDefinition($childName, QueryRequirementInterface $queryRequirement)
    {
        return array_key_exists($childName, $this->childDefinitionNodes) ?
            $this->childDefinitionNodes[$childName] :
            new DynamicUnknownChildWidgetDefinitionNode($childName, $queryRequirement);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionReferences()
    {
        return $this->eventDefinitionReferenceNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        $this->attributeBagModelNode->validateStaticExpressionBag(
            $validationContext,
            $attributeExpressionBagNode,
            'attributes for primitive "' . $this->name . '" widget of library "' . $this->libraryName . '"'
        );

        // Check there are no required children that are missing an expression
        foreach ($this->childDefinitionNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $childWidgetNodes) && $definitionNode->isRequired()) {
                $validationContext->addGenericViolation(sprintf(
                    'Primitive widget is missing required child "%s"',
                    $childName
                ));
            }
        }

        // Check there are no child widgets that aren't needed/are extra
        foreach ($childWidgetNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $this->childDefinitionNodes)) {
                $validationContext->addGenericViolation(sprintf(
                    'Primitive widget has an unnecessary extra child widget "%s"',
                    $childName
                ));
            }
        }

        // Check all child widgets provided can only ever evaluate to valid primitives
        // for their corresponding children
        foreach ($this->childDefinitionNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $childWidgetNodes)) {
                // Skip any undefined children as we won't be able to fetch them.
                // Validation should already have been marked failed above
                continue;
            }

            $childWidgetNode = $childWidgetNodes[$childName];

            $definitionNode->validateWidget($childWidgetNode, $validationContext);
        }
    }
}
