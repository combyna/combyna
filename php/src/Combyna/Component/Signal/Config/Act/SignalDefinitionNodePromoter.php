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

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Signal\SignalDefinitionCollectionInterface;
use Combyna\Component\Signal\SignalFactoryInterface;

/**
 * Class SignalDefinitionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var SignalFactoryInterface
     */
    private $signalFactory;

    /**
     * @param SignalFactoryInterface $signalFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(SignalFactoryInterface $signalFactory, BagNodePromoter $bagNodePromoter)
    {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->signalFactory = $signalFactory;
    }

    /**
     * Promotes a set of SignalDefinitionNodes to a SignalDefinitionCollection
     *
     * @param SignalDefinitionNode[] $signalDefinitionNodes
     * @param string $libraryName
     * @return SignalDefinitionCollectionInterface
     */
    public function promoteCollection(array $signalDefinitionNodes, $libraryName)
    {
        $signalDefinitions = [];

        foreach ($signalDefinitionNodes as $signalDefinitionNode) {
            $signalDefinitions[] = $this->signalFactory->createSignalDefinition(
                $libraryName,
                $signalDefinitionNode->getSignalName(),
                $this->bagNodePromoter->promoteFixedStaticBagModel($signalDefinitionNode->getPayloadStaticBagModel())
            );
        }

        return $this->signalFactory->createSignalDefinitionCollection($signalDefinitions, $libraryName);
    }
}
