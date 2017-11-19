<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Ui\Store\UiStoreFactoryInterface;
use Combyna\Component\Ui\Store\ViewStoreInterface;

/**
 * Class ViewStoreNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var ViewStoreQueryNodePromoter
     */
    private $queryNodePromoter;

    /**
     * @var ViewStoreSignalHandlerNodePromoter
     */
    private $signalHandlerNodePromoter;

    /**
     * @var UiStoreFactoryInterface
     */
    private $storeFactory;

    /**
     * @param UiStoreFactoryInterface $storeFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param ViewStoreQueryNodePromoter $queryNodePromoter
     * @param ViewStoreSignalHandlerNodePromoter $signalHandlerNodePromoter
     */
    public function __construct(
        UiStoreFactoryInterface $storeFactory,
        BagNodePromoter $bagNodePromoter,
        ViewStoreQueryNodePromoter $queryNodePromoter,
        ViewStoreSignalHandlerNodePromoter $signalHandlerNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->queryNodePromoter = $queryNodePromoter;
        $this->signalHandlerNodePromoter = $signalHandlerNodePromoter;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Creates a ViewStore from a ViewStoreNode
     *
     * @param string $viewName
     * @param ViewStoreNode|null $viewStoreNode
     * @return ViewStoreInterface|null
     */
    public function promote($viewName, ViewStoreNode $viewStoreNode = null)
    {
        if ($viewStoreNode === null) {
            return $this->storeFactory->createNullViewStore($viewName);
        }

        $slotBagModel = $this->bagNodePromoter->promoteFixedStaticBagModel($viewStoreNode->getSlotBagModel());
        $signalHandlers = $this->signalHandlerNodePromoter->promoteCollection($viewStoreNode->getSignalHandlers());
        $commands = [];
        $queries = $this->queryNodePromoter->promoteCollection($viewStoreNode->getQueries());

        return $this->storeFactory->createViewStore(
            $viewName,
            $slotBagModel,
            $signalHandlers,
            $commands,
            $queries
        );
    }
}
