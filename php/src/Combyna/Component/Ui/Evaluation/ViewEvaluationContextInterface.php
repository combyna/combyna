<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\Store\Evaluation\StoreEvaluationContextInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface ViewEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewEvaluationContextInterface extends EvaluationContextInterface
{
    /**
     * Dispatches an event on the parent widget (as per the state tree)
     *
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param EventInterface $event
     * @param WidgetInterface $initialWidget
     * @return ProgramStateInterface
     */
    public function bubbleEventToParent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetInterface $initialWidget
    );

    /**
     * Creates a new StoreEvaluationContext as a child of the current one,
     * with the specified store state available for slots etc.
     *
     * @param UiStoreStateInterface $storeState
     * @return StoreEvaluationContextInterface
     */
    public function createSubStoreContext(UiStoreStateInterface $storeState);

    /**
     * {@inheritdoc}
     *
     * @return ViewEvaluationContextInterface|null
     */
    public function getParentContext();

    /**
     * Fetches the path to the current UI component
     *
     * @return string[]|int[]
     */
    public function getPath();
}
