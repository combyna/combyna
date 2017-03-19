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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Ui\CompoundWidgetDefinition;
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
     * @param FixedStaticBagModelNode $attributeBagModelNode
     */
    public function __construct($libraryName, $widgetDefinitionName, FixedStaticBagModelNode $attributeBagModelNode)
    {
        $this->attributeBagModelNode = $attributeBagModelNode;
        $this->libraryName = $libraryName;
        $this->name = $widgetDefinitionName;
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
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->attributeBagModelNode->validate($subValidationContext);
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
    }
}
