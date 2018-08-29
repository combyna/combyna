<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Event\Expression\EventPayloadExpression;
use Combyna\Component\Event\Validation\Constraint\EventDefinitionHasPayloadStaticConstraint;
use Combyna\Component\Event\Validation\Query\CurrentEventPayloadStaticTypeQuery;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Trigger\Validation\Constraint\InsideTriggerConstraint;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class EventPayloadExpressionNode
 *
 * Fetches a static from the current event's payload
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventPayloadExpressionNode extends AbstractExpressionNode
{
    const TYPE = EventPayloadExpression::TYPE;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $staticName
     */
    public function __construct($staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new InsideTriggerConstraint());
        $specBuilder->addConstraint(new EventDefinitionHasPayloadStaticConstraint($this->staticName));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(new CurrentEventPayloadStaticTypeQuery($this->staticName), $this);
    }

    /**
     * Fetches the name of the payload static to be fetched
     *
     * @return string
     */
    public function getStaticName()
    {
        return $this->staticName;
    }
}
