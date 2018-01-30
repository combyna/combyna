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
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
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
     * @var FixedStaticBagModelNode
     */
    private $attributeBagModelNode;

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
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param FixedStaticBagModelNode $attributeBagModelNode
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     * @param WidgetNodeInterface $rootWidgetNode
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        FixedStaticBagModelNode $attributeBagModelNode,
        array $eventDefinitionReferenceNodes,
        WidgetNodeInterface $rootWidgetNode
    ) {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->eventDefinitionReferenceNodes = $eventDefinitionReferenceNodes;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
        $this->rootWidgetNode = $rootWidgetNode;
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
     * Fetches the widget that defines the content of all widgets of this definition
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
    public function getWidgetDefinitionName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validate($subValidationContext);
        $this->rootWidgetNode->validate($subValidationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validateStaticExpressionBag(
            $subValidationContext,
            $attributeExpressionBagNode,
            'attributes for compound "' . $this->name . '" widget of library "' . $this->libraryName . '"'
        );

        // TODO: Check that all EventDefinitionReferences are valid (ie. reference valid definitions)
    }
}
