<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
 * Interface StoreQueryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StoreQueryInterface
{
    /**
     * Fetches the unique name of this query within the store
     *
     * @return string
     */
    public function getName();

    /**
     * Makes this query, using the provided argument statics
     *
     * @param StaticBagInterface $argumentStaticBag
     * @param UiEvaluationContextInterface $evaluationContext
     * @param UiStoreStateInterface $storeState
     * @return StaticInterface
     */
    public function make(
        StaticBagInterface $argumentStaticBag,
        UiEvaluationContextInterface $evaluationContext,
        UiStoreStateInterface $storeState
    );
}
