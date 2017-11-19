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
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Store\Config\Act\QueryNode;
use Combyna\Component\Ui\Store\Query\StoreQueryCollectionInterface;
use Combyna\Component\Ui\Store\Query\StoreQueryInterface;
use Combyna\Component\Ui\Store\UiStoreFactoryInterface;

/**
 * Class ViewStoreQueryNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var UiStoreFactoryInterface
     */
    private $storeFactory;

    /**
     * @param UiStoreFactoryInterface $storeFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(
        UiStoreFactoryInterface $storeFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Creates a StoreQuery from a QueryNode
     *
     * @param QueryNode $queryNode
     * @return StoreQueryInterface
     */
    public function promote(QueryNode $queryNode)
    {
        return $this->storeFactory->createQuery(
            $queryNode->getName(),
            $this->bagNodePromoter->promoteFixedStaticBagModel($queryNode->getParameterBagModel()),
            $this->expressionNodePromoter->promote($queryNode->getExpression())
        );
    }

    /**
     * Creates a StoreQueryCollection
     *
     * @param QueryNode[] $queryNodes
     * @return StoreQueryCollectionInterface
     */
    public function promoteCollection(array $queryNodes)
    {
        $storeQueries = array_map(function (QueryNode $queryNode) {
            return $this->promote($queryNode);
        }, $queryNodes);

        return $this->storeFactory->createQueryCollection($storeQueries);
    }
}
