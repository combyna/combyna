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

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;

/**
 * Interface WidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetInterface
{
    /**
     * Creates a WidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param WidgetStateInterface|null $widgetState
     * @return WidgetEvaluationContextInterface
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    );

    /**
     * Creates an event to dispatch for a rendered instance of this widget
     *
     * @param string $libraryName
     * @param string $eventName
     * @param array $payloadNatives
     * @param ViewEvaluationContextInterface $evaluationContext
     * @return EventInterface
     * @throws EventDefinitionNotReferencedByWidgetException
     * @throws LibraryNotInstalledException
     */
    public function createEvent(
        $libraryName,
        $eventName,
        array $payloadNatives,
        ViewEvaluationContextInterface $evaluationContext
    );

    /**
     * Creates an initial state for the widget
     *
     * @param string|int $name
     * @param ViewEvaluationContextInterface $evaluationContext
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @return WidgetStateInterface
     */
    public function createInitialState(
        $name,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    );

    /**
     * Determines whether this widget, or any of its descendants, define the specified capture
     *
     * @param string $captureName
     * @return bool
     */
    public function descendantsSetCaptureInclusive($captureName);

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
     * Evaluates and then returns the specified attribute of this widget
     *
     * @param string $attributeName
     * @param ViewEvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext);

    /**
     * Fetches the capture expression bag
     *
     * @return ExpressionBagInterface
     */
    public function getCaptureExpressionBag();

    /**
     * Fetches the capture static bag model
     *
     * @return FixedStaticBagModelInterface
     */
    public function getCaptureStaticBagModel();

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
     * Fetches this widget's parent, if any
     *
     * @return WidgetInterface|null
     */
    public function getParentWidget();

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
     * Returns true if the widget can be rendered directly (ie. is primitive or core),
     * or false if only some of its descendants may be rendered (eg. is compound)
     *
     * @return bool
     */
    public function isRenderable();

    /**
     * Re-evaluates the state for the widget, using the old state as a base.
     * If the newly evaluated state is the same as the old one,
     * the original state object will be returned
     *
     * @param WidgetStateInterface $oldState
     * @param ViewEvaluationContextInterface $evaluationContext
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @return WidgetStateInterface
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    );
}
