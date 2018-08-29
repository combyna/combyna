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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;
use LogicException;

/**
 * Class UnknownLibraryForWidgetDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownLibraryForWidgetDefinitionNode extends AbstractActNode implements WidgetDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-library-for-widget-definition';

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var QueryRequirementInterface
     */
    private $queryRequirement;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param QueryRequirementInterface $queryRequirement
     */
    public function __construct($libraryName, $widgetDefinitionName, QueryRequirementInterface $queryRequirement)
    {
        $this->libraryName = $libraryName;
        $this->queryRequirement = $queryRequirement;
        $this->widgetDefinitionName = $widgetDefinitionName;

        // Apply the validation for this dynamically created ACT node
        $queryRequirement->adoptDynamicActNode($this);
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
                    'Library "%s" does not exist in order to define widget definition "%s"',
                    $this->libraryName,
                    $this->widgetDefinitionName
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf(
                'Attribute static bag for undefined widget "%s" of undefined library "%s"',
                $this->widgetDefinitionName,
                $this->libraryName
            ),
            $this->queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getChildDefinition($childName, QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownChildWidgetDefinitionNode($childName, $queryRequirement);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionReferences()
    {
        // We should never reach this point, as validation should have failed
        throw new LogicException('Unknown widget definition should not be queried for its event definition references');
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
