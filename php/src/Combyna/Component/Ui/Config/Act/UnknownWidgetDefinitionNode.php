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
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class UnknownWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownWidgetDefinitionNode extends AbstractActNode implements WidgetDefinitionNodeInterface
{
    const TYPE = 'unknown-widget-definition';

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     */
    public function __construct($libraryName, $widgetDefinitionName)
    {
        $this->libraryName = $libraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBagModel()
    {
        // We should never reach this point, as validation should have failed
        throw new LogicException('Unknown widget definition should not be queried for its attribute model');
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitions()
    {
        // We should never reach this point, as validation should have failed
        throw new LogicException('Unknown widget definition should not be queried for its event definitions');
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
        return $this->widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $subValidationContext->addGenericViolation(
            'Widget definition "' . $this->widgetDefinitionName . '" of library "' .
            $this->libraryName . '" is not defined'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        // Nothing to do: ::validate(...) above will make sure that validation fails
    }
}
