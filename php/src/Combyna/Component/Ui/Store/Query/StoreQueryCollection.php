<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Query;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use InvalidArgumentException;

/**
 * Class StoreQueryCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StoreQueryCollection implements StoreQueryCollectionInterface
{
    /**
     * @var StoreQueryInterface[]
     */
    private $storeQueries = [];

    /**
     * @param StoreQueryInterface[] $storeQueries
     */
    public function __construct(array $storeQueries)
    {
        foreach ($storeQueries as $storeQuery) {
            // Index queries by name to simplify lookups
            $this->storeQueries[$storeQuery->getName()] = $storeQuery;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function makeQuery(
        $queryName,
        StaticBagInterface $argumentStaticBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiStoreStateInterface $storeState
    ) {
        if (!array_key_exists($queryName, $this->storeQueries)) {
            throw new InvalidArgumentException(sprintf('Collection has no query with name "%s"', $queryName));
        }

        return $this->storeQueries[$queryName]->make($argumentStaticBag, $evaluationContext, $storeState);
    }
}
