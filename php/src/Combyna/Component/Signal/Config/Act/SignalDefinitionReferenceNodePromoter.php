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

use Combyna\Component\Signal\SignalDefinitionReferenceInterface;
use Combyna\Component\Signal\SignalFactoryInterface;

/**
 * Class SignalDefinitionReferenceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionReferenceNodePromoter
{
    /**
     * @var SignalFactoryInterface
     */
    private $signalFactory;

    /**
     * @param SignalFactoryInterface $signalFactory
     */
    public function __construct(SignalFactoryInterface $signalFactory)
    {
        $this->signalFactory = $signalFactory;
    }

    /**
     * Promotes a SignalDefinitionReferenceNode to a SignalDefinitionReference
     *
     * @param SignalDefinitionReferenceNode $signalDefinitionReferenceNode
     * @return SignalDefinitionReferenceInterface
     */
    public function promote(SignalDefinitionReferenceNode $signalDefinitionReferenceNode)
    {
        return $this->signalFactory->createSignalDefinitionReference(
            $signalDefinitionReferenceNode->getLibraryName(),
            $signalDefinitionReferenceNode->getSignalName()
        );
    }
}
