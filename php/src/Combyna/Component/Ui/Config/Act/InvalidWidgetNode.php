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

use Combyna\Component\Bag\Config\Act\DynamicUnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

/**
 * Class InvalidWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'invalid-widget';

    /**
     * @var string
     */
    private $contextDescription;

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
     * @param string $contextDescription
     */
    public function __construct($libraryName, $widgetDefinitionName, $contextDescription)
    {
        $this->contextDescription = $contextDescription;
        $this->libraryName = $libraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
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
                    'Widget "%s" of library "%s" is invalid: %s',
                    $this->widgetDefinitionName,
                    $this->libraryName,
                    $this->contextDescription
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureExpressionBag(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownExpressionBagNode(
            sprintf('%s capture set', $this->contextDescription),
            $queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel(QueryRequirementInterface $queryRequirement)
    {
        return new DynamicUnknownFixedStaticBagModelNode(
            sprintf('%s capture model', $this->contextDescription),
            $queryRequirement
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return 'unknown';
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return []; // Unknown widgets define no tags
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibilityExpression()
    {
        // TODO: Remove visibility expressions
        return new UnknownExpressionNode(sprintf('%s visibility expression', $this->contextDescription));
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'unknown';
    }
}
