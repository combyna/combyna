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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Store\Query\StoreQueryCollectionInterface;
use Combyna\Component\Ui\Store\Signal\ViewStoreSignalHandlerInterface;
use InvalidArgumentException;

/**
 * Class ViewStore
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStore implements ViewStoreInterface
{
    /**
     * @var StoreCommandInterface[]
     */
    private $commands;

    /**
     * @var StoreQueryCollectionInterface
     */
    private $queryCollection;

    /**
     * @var ViewStoreSignalHandlerInterface[]
     */
    private $signalHandlers;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $slotStaticBagModel;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

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
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param string $viewName
     * @param FixedStaticBagModelInterface $slotStaticBagModel
     * @param ViewStoreSignalHandlerInterface[] $signalHandlers
     * @param StoreCommandInterface[] $commands
     * @param StoreQueryCollectionInterface $queryCollection
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        $viewName,
        FixedStaticBagModelInterface $slotStaticBagModel,
        array $signalHandlers,
        array $commands,
        StoreQueryCollectionInterface $queryCollection
    ) {
        $this->commands = $commands;
        $this->queryCollection = $queryCollection;
        $this->signalHandlers = $signalHandlers;
        $this->slotStaticBagModel = $slotStaticBagModel;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiStateFactory = $uiStateFactory;
        $this->viewName = $viewName;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidSlotStaticBag(StaticBagInterface $slotStaticBag)
    {
        $this->slotStaticBagModel->assertValidStaticBag($slotStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(EvaluationContextInterface $rootEvaluationContext)
    {
        $slotStaticBag = $this->slotStaticBagModel->createDefaultStaticBag($rootEvaluationContext);

        return $this->uiStateFactory->createViewStoreState($this->viewName, $slotStaticBag);
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
        foreach ($this->signalHandlers as $signalHandler) {
            // Each signal handler gets its own evaluation context, in case the view store state
            // was changed by a previous signal handler in this loop
            $storeEvaluationContext = $this->uiEvaluationContextFactory->createViewStoreEvaluationContext(
                $viewEvaluationContext,
                $viewStoreState
            );

            $viewStoreState = $signalHandler->handleSignal($viewStoreState, $signal, $storeEvaluationContext);
        }

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
        try {
            return $this->queryCollection->makeQuery($name, $argumentStaticBag, $evaluationContext, $viewStoreState);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(sprintf(
                'Store for view "%s" has no query "%s"',
                $this->viewName,
                $name
            ));
        }
    }
}
