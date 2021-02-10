<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Bag\Config\Act\UnknownFixedStaticBagModelNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\NullValidationContext;

/**
 * Class UnknownSignalDefinitionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownSignalDefinitionNode extends AbstractActNode implements SignalDefinitionNodeInterface, DynamicActNodeInterface
{
    const TYPE = 'unknown-signal-definition';

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
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($libraryName, $signalName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;

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
                    'Library "%s" does not define signal "%s"',
                    $this->libraryName,
                    $this->signalName
                )
            )
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
    public function getPayloadStaticBagModel()
    {
        return new UnknownFixedStaticBagModelNode(
            sprintf(
                'Payload static bag for undefined signal "%s" of defined library "%s"',
                $this->signalName,
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
                'Payload static "%s" for undefined signal "%s" of defined library "%s"',
                $staticName,
                $this->signalName,
                $this->libraryName
            ),
            new NullValidationContext()
        );
    }

    /**
     * Fetches the unique name of the signal
     *
     * @return string
     */
    public function getSignalName()
    {
        return $this->signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function isBroadcast()
    {
        return false; // Unknown signals cannot be dispatched, let alone broadcast
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined()
    {
        return false; // Unknown signal definition
    }
}
