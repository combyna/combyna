<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Signal;

use Combyna\Component\Signal\SignalHandlerInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;

/**
 * Interface ViewStoreSignalHandlerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreSignalHandlerInterface extends SignalHandlerInterface
{
    /**
     * Handles a signal with one or more signal handlers, if registered. If no signal handlers
     * are registered or all guard checks fail, the original state will be returned
     *
     * @param ViewStoreStateInterface $viewStoreState
     * @param SignalInterface $signal
     * @param ViewStoreEvaluationContextInterface $storeEvaluationContext
     * @return ViewStoreStateInterface
     */
    public function handleSignal(
        ViewStoreStateInterface $viewStoreState,
        SignalInterface $signal,
        ViewStoreEvaluationContextInterface $storeEvaluationContext
    );
}
