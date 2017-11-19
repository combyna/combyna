<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use InvalidArgumentException;

/**
 * Class NullViewStore
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullViewStore implements NullViewStoreInterface
{
    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var string
     */
    private $viewName;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param string $viewName
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        $viewName
    ) {
        $this->uiStateFactory = $uiStateFactory;
        $this->viewName = $viewName;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidSlotStaticBag(StaticBagInterface $slotStaticBag)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(EvaluationContextInterface $rootEvaluationContext)
    {
        return $this->uiStateFactory->createNullViewStoreState($this->viewName);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        SignalInterface $signal,
        ViewStoreStateInterface $viewStoreState,
        ViewEvaluationContextInterface $viewEvaluationContext
    ) {
        return $viewStoreState;
    }

    /**
     * {@inheritdoc}
     */
    public function makeQuery(
        $name,
        StaticBagInterface $argumentStaticBag,
        UiEvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    ) {
        throw new InvalidArgumentException(sprintf(
            'Null store for view "%s" cannot respond to query "%s", as they define none',
            $this->viewName,
            $name
        ));
    }
}
