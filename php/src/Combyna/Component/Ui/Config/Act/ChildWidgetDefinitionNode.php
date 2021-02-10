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
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ChildWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildWidgetDefinitionNode extends AbstractActNode implements ChildWidgetDefinitionNodeInterface
{
    const TYPE = 'child-widget-definition';

    /**
     * @var string
     */
    private $childName;

    /**
     * @param string $childName
     */
    public function __construct($childName)
    {
        $this->childName = $childName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // TODO: Do a forward-check to ensure this child is actually used somewhere inside the root widget?
    }

    /**
     * {@inheritdoc}
     */
    public function getChildName()
    {
        return $this->childName;
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
    public function isRequired()
    {
        return true; // TODO: Child widgets are always required for now
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(WidgetNodeInterface $widgetNode, ValidationContextInterface $validationContext)
    {
        // Nothing to do... yet (until eg. we want to restrict what types of widget could be passed for this child)
    }
}
