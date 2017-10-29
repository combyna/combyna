<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Store\Config\Act\QueryNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewStoreNode
 *
 * Defines a store with commands, queries and signal handlers for a view
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreNode extends AbstractActNode
{
    const TYPE = 'view-store';

    /**
     * @var QueryNode[]
     */
    private $queryNodes;

    /**
     * @var SignalHandlerNode[]
     */
    private $signalHandlerNodes;

    /**
     * @var FixedStaticBagModelNode
     */
    private $slotBagModelNode;

    /**
     * @param FixedStaticBagModelNode $slotBagModelNode
     * @param QueryNode[] $queryNodes
     * @param SignalHandlerNode[] $signalHandlerNodes
     */
    public function __construct(FixedStaticBagModelNode $slotBagModelNode, array $queryNodes, array $signalHandlerNodes)
    {
        $this->queryNodes = $queryNodes;
        $this->signalHandlerNodes = $signalHandlerNodes;
        $this->slotBagModelNode = $slotBagModelNode;
    }

    /**
     * Fetches the queries this store defines
     *
     * @return QueryNode[]
     */
    public function getQueries()
    {
        return $this->queryNodes;
    }

    /**
     * Fetches the signal handlers this store has
     *
     * @return SignalHandlerNode[]
     */
    public function getSignalHandlers()
    {
        return $this->signalHandlerNodes;
    }

    /**
     * Fetches the static bag model for slots in this store
     *
     * @return FixedStaticBagModelNode
     */
    public function getSlotBagModel()
    {
        return $this->slotBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->slotBagModelNode->validate($subValidationContext);
    }
}
