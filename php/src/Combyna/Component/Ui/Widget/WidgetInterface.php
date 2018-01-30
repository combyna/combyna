<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;

/**
 * Interface WidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetInterface
{
    /**
     * Creates an event to dispatch for a rendered instance of this widget
     *
     * @param string $libraryName
     * @param string $eventName
     * @param StaticBagInterface $payloadStaticBag
     * @return EventInterface
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag);

    /**
     * Creates an initial state for the widget
     *
     * @param UiEvaluationContextInterface $evaluationContext
     * @return WidgetStateInterface
     */
    public function createInitialState(UiEvaluationContextInterface $evaluationContext);

    /**
     * Dispatches an event
     *
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param EventInterface $event
     * @param WidgetEvaluationContextInterface $widgetEvaluationContext
     * @return ProgramStateInterface
     */
    public function dispatchEvent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    );

    /**
     * Fetches the unique name for the definition of this widget
     *
     * @return string
     */
    public function getDefinitionLibraryName();

    /**
     * Fetches the unique name for the definition of this widget
     *
     * @return string
     */
    public function getDefinitionName();

    /**
     * Fetches a child or descendant of a child of this widget by its path
     *
     * @param string[]|int[] $names
     * @return WidgetInterface
     */
    public function getDescendantByPath(array $names);

    /**
     * Fetches the unique name of this widget within its parent
     *
     * @return string|int
     */
    public function getName();

    /**
     * Fetches the path to this widget, with its view name and all ancestor names
     *
     * @return string[]|int[]
     */
    public function getPath();

    /**
     * Returns true if this widget has the specified tag, false otherwise
     *
     * @param string $tag
     * @return bool
     */
    public function hasTag($tag);

    /**
     * Returns true if the widget can be rendered directly,
     * or false if only some of its descendants may be rendered
     *
     * @return bool
     */
    public function isRenderable();
}
