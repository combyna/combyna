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
use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class UnknownWidgetDefinitionTypeNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownWidgetDefinitionTypeNode extends AbstractActNode implements WidgetDefinitionNodeInterface
{
    const TYPE = 'unknown-widget-definition-type';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string $type
     */
    public function __construct($libraryName, $widgetDefinitionName, $type)
    {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->libraryName = $libraryName;
        $this->type = $type;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);

        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Widget definition "%s" of library "%s" is of unknown type "%s"',
                    $this->widgetDefinitionName,
                    $this->libraryName,
                    $this->type
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function definesValue($valueName)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf(
                'Attribute static bag for widget "%s" of library "%s" with unknown type "%s"',
                $this->widgetDefinitionName,
                $this->libraryName,
                $this->type
            ),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getChildDefinition($childName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return new UnknownChildWidgetDefinitionNode($childName, $dynamicActNodeAdopter);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionReferences()
    {
        // We should never reach this point, as validation should have failed
        throw new LogicException(
            'Widget definition of unknown type should not be queried for its event definition references'
        );
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
    public function getValueType($valueName)
    {
        return new UnresolvedType(
            sprintf(
                'Value "%s" for widget "%s" of library "%s" with unknown type "%s"',
                $valueName,
                $this->widgetDefinitionName,
                $this->libraryName,
                $this->type
            )
        );
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
    public function isDefined()
    {
        return false; // Unknown widget definition
    }

    /**
     * {@inheritdoc}
     */
    public function validateWidget(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $attributeExpressionBagNode,
        array $childWidgetNodes
    ) {
        // Nothing to do: the behaviour spec will make sure that validation fails
    }
}
