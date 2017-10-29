<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store;

use AssertionError;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;

/**
 * Interface ViewStoreInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreInterface
{
    /**
     * Checks that the bag contains a valid static for all slots in this store, and only those
     *
     * @param StaticBagInterface $slotStaticBag
     * @throws AssertionError Throws when assertion fails
     */
    public function assertValidSlotStaticBag(StaticBagInterface $slotStaticBag);

    /**
     * Creates an initial state for the page view
     *
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return ViewStoreStateInterface
     */
    public function createInitialState(EvaluationContextInterface $rootEvaluationContext);

    /**
     * Fetches the unique name for the view this store belongs to
     *
     * @return string
     */
    public function getViewName();

    /**
     * Handles a signal with one or more signal handlers, if registered. If no signal handlers
     * are registered or all guard checks fail, the original state will be returned
     *
     * @param SignalInterface $signal
     * @param ViewStoreStateInterface $viewStoreState
     * @param ViewEvaluationContextInterface $viewEvaluationContext
     * @return ViewStoreStateInterface
     */
    public function handleSignal(
        SignalInterface $signal,
        ViewStoreStateInterface $viewStoreState,
        ViewEvaluationContextInterface $viewEvaluationContext
    );

    /**
     * Makes the specified query against this store, returning its result
     *
     * @param string $name
     * @param StaticBagInterface $argumentStaticBag
     * @param UiEvaluationContextInterface $evaluationContext
     * @param ViewStoreStateInterface $viewStoreState
     * @return StaticInterface
     */
    public function makeQuery(
        $name,
        StaticBagInterface $argumentStaticBag,
        UiEvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    );
}
