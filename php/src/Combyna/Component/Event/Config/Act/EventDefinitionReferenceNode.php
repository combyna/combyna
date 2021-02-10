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
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Event\Validation\Constraint\EventDefinitionExistsConstraint;

/**
 * Class EventDefinitionReferenceNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReferenceNode extends AbstractActNode
{
    const TYPE = 'event-definition-reference';

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $eventName
     */
    public function __construct($libraryName, $eventName)
    {
        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new EventDefinitionExistsConstraint(
                $this->libraryName,
                $this->eventName
            )
        );
    }

    /**
     * Fetches the unique name of the event
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Fetches the unique name of the library that defines this event definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
