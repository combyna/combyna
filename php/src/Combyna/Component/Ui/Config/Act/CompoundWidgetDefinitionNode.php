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
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Ui\Validation\Context\Specifier\CompoundWidgetDefinitionContextSpecifier;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CompoundWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionNode extends AbstractActNode implements WidgetDefinitionNodeInterface
{
    const TYPE = CompoundWidgetDefinition::TYPE;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $attributeBagModelNode;

    /**
     * @var ChildWidgetDefinitionNodeInterface[]
     */
    private $childDefinitionNodes;

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

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
     * @var WidgetNodeInterface
     */
    private $rootWidgetNode;

    /**
     * @var ExpressionBagNode
     */
    private $valueExpressionBagNode;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param FixedStaticBagModelNodeInterface $attributeBagModelNode
     * @param ExpressionBagNode $valueExpressionBagNode
     * @param ChildWidgetDefinitionNodeInterface[] $childDefinitionNodes
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     * @param WidgetNodeInterface $rootWidgetNode
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        FixedStaticBagModelNodeInterface $attributeBagModelNode,
        ExpressionBagNode $valueExpressionBagNode,
        array $childDefinitionNodes,
        array $eventDefinitionReferenceNodes,
        WidgetNodeInterface $rootWidgetNode
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->childDefinitionNodes = $childDefinitionNodes;
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->eventDefinitionReferenceNodes = $eventDefinitionReferenceNodes;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
        $this->rootWidgetNode = $rootWidgetNode;
        $this->valueExpressionBagNode = $valueExpressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->attributeBagModelNode);
        $specBuilder->addChildNode($this->dynamicContainerNode);

        foreach ($this->childDefinitionNodes as $childDefinitionNode) {
            $specBuilder->addChildNode($childDefinitionNode);
        }

        foreach ($this->eventDefinitionReferenceNodes as $eventDefinitionReferenceNode) {
            $specBuilder->addChildNode($eventDefinitionReferenceNode);
        }

        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            // Only give the root widget node tree and widget value defaults
            // the compound widget definition context,
            // as compound widget attrs cannot reference other compound widget attrs
            // for the same widget
            $subSpecBuilder->defineValidationContext(
                new CompoundWidgetDefinitionContextSpecifier()
            );

            $subSpecBuilder->addChildNode($this->rootWidgetNode);
            $subSpecBuilder->addChildNode($this->valueExpressionBagNode);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function definesValue($valueName)
    {
        return $this->valueExpressionBagNode->hasExpression($valueName);
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
    public function getChildDefinition($childName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return array_key_exists($childName, $this->childDefinitionNodes) ?
            $this->childDefinitionNodes[$childName] :
            new UnknownChildWidgetDefinitionNode($childName, $dynamicActNodeAdopter);
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
    public function getIdentifier()
    {
        return 'compound-widget-def:' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the widget that defines the content of all widgets of this definition
     *
     * @return WidgetNodeInterface
     */
    public function getRootWidget()
    {
        return $this->rootWidgetNode;
    }

    /**
     * Fetches the fixed static bag model for values of widgets with this definition
     *
     * @return ExpressionBagNode
     */
    public function getValueExpressionBag()
    {
        return $this->valueExpressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueType($valueName)
    {
        return $this->dynamicContainerNode->determineType(
            $this->valueExpressionBagNode
                ->getExpression($valueName)
                ->getResultTypeDeterminer()
        );
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
            'attributes for compound "' . $this->name . '" widget of library "' . $this->libraryName . '"'
        );

        // Check there are no required children that are missing an expression
        foreach ($this->childDefinitionNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $childWidgetNodes) && $definitionNode->isRequired()) {
                $validationContext->addGenericViolation(sprintf(
                    'Compound widget is missing required child "%s"',
                    $childName
                ));
            }
        }

        // Check there are no child widgets that aren't needed/are extra
        foreach ($childWidgetNodes as $childName => $definitionNode) {
            if (!array_key_exists($childName, $this->childDefinitionNodes)) {
                $validationContext->addGenericViolation(sprintf(
                    'Compound widget has an unnecessary extra child widget "%s"',
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
