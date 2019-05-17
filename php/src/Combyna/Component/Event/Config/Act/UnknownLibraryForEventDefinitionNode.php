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

use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;

/**
 * Class UnknownLibraryForEventDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownLibraryForEventDefinitionNode extends AbstractActNode implements EventDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-library-for-event-definition';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

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
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($libraryName, $eventName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->eventName = $eventName;
        $this->libraryName = $libraryName;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
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
                    'Library "%s" does not exist in order to define event "%s"',
                    $this->libraryName,
                    $this->eventName
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf(
                'Payload static bag for undefined event "%s" of undefined library "%s"',
                $this->eventName,
                $this->libraryName
            ),
            $this->dynamicContainerNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticType($staticName)
    {
        return new UnresolvedType(
            sprintf(
                'Payload static "%s" for undefined event "%s" of undefined library "%s"',
                $staticName,
                $this->eventName,
                $this->libraryName
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false; // Unknown library and unknown event definition
    }
}
