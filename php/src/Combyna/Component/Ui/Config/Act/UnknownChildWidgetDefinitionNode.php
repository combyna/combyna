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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class UnknownChildWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownChildWidgetDefinitionNode extends AbstractActNode implements ChildWidgetDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-child-widget-definition';

    /**
     * @var string
     */
    private $childName;

    /**
     * @param string $childName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($childName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->childName = $childName;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Child "%s" is not defined by the widget definition',
                    $this->childName
                )
            )
        );
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
        return false; // Unknown child widget
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return false; // Validation will fail anyway
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(WidgetNodeInterface $widgetNode, ValidationContextInterface $validationContext)
    {
        // Nothing to do, validation should already have failed
    }
}
