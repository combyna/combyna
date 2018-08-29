<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Event\Validation\Query\CurrentEventPayloadStaticTypeQuery;
use Combyna\Component\Event\Validation\Query\EventDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Trigger\Validation\Query\InsideTriggerQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class TriggerSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerSubValidationContext implements TriggerSubValidationContextInterface
{
    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var TriggerNode
     */
    private $triggerNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $triggerNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param TriggerNode $triggerNode
     * @param BehaviourSpecInterface $triggerNodeBehaviourSpec
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        TriggerNode $triggerNode,
        BehaviourSpecInterface $triggerNodeBehaviourSpec
    ) {
        $this->parentContext = $parentContext;
        $this->triggerNode = $triggerNode;
        $this->triggerNodeBehaviourSpec = $triggerNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getActNode()
    {
        return $this->triggerNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->triggerNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $path = $this->parentContext->getPath();

        if ($path !== '') {
            $path .= '.';
        }

        $path .= sprintf(
            '[trigger:%s.%s]',
            $this->triggerNode->getEventDefinitionReference()->getLibraryName(),
            $this->triggerNode->getEventDefinitionReference()->getEventName()
        );

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            CurrentEventPayloadStaticTypeQuery::class => [$this, 'queryForCurrentEventPayloadStaticType'],
            InsideTriggerQuery::class => [$this, 'queryForInsideTrigger']
        ];
    }

    /**
     * Fetches the type of the specified payload static of the event this trigger will fire for
     *
     * @param CurrentEventPayloadStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForCurrentEventPayloadStaticType(
        CurrentEventPayloadStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $validationContext->queryForResultType(
            // Make another query for the actual event's payload static,
            // adding the library and event name for the current trigger
            new EventDefinitionPayloadStaticTypeQuery(
                $this->triggerNode->getEventDefinitionReference()->getLibraryName(),
                $this->triggerNode->getEventDefinitionReference()->getEventName(),
                $query->getPayloadStaticName()
            ),
            $this->triggerNode
        );
    }

    /**
     * Determines whether we are inside a trigger
     *
     * @return bool
     */
    public function queryForInsideTrigger()
    {
        return true;
    }
}
