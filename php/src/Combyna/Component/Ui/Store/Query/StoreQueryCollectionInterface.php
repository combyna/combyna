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
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;

/**
 * Interface StoreQueryCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StoreQueryCollectionInterface
{
    /**
     * Makes a query in this collection, returning its static result
     *
     * @param string $queryName
     * @param StaticBagInterface $argumentStaticBag
     * @param UiEvaluationContextInterface $evaluationContext
     * @param UiStoreStateInterface $storeState
     * @return StaticInterface
     */
    public function makeQuery(
        $queryName,
        StaticBagInterface $argumentStaticBag,
        UiEvaluationContextInterface $evaluationContext,
        UiStoreStateInterface $storeState
    );
}
