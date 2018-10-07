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
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;

/**
 * Interface WidgetDefinitionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetDefinitionInterface
{
    /**
     * Checks that the provided static bag is a valid set of attributes for a widget of this definition
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Creates a WidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param DefinedWidgetInterface $widget
     * @param DefinedWidgetStateInterface $widgetState
     * @return WidgetEvaluationContextInterface
     */
    public function createEvaluationContextForWidget(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState
    );

    /**
     * Creates an event to dispatch for a rendered instance of a widget of this definition
     *
     * @param string $libraryName
     * @param string $eventName
     * @param StaticBagInterface $payloadStaticBag
     * @return EventInterface
     * @throws EventDefinitionNotReferencedByWidgetException
     * @throws LibraryNotInstalledException
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag);

    /**
     * Creates a DefinedPrimitiveWidgetState
     *
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @param ViewEvaluationContextInterface $evaluationContext
     * @return DefinedWidgetStateInterface
     */
    public function createInitialStateForWidget(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        ViewEvaluationContextInterface $evaluationContext
    );

    /**
     * Fetches the name of the library this widget definition is defined by
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for this type of widget
     *
     * @return string
     */
    public function getName();

    /**
     * Renderable widgets may be rendered directly, without needing to be "resolved" further.
     * For example, a compound widget is not renderable as its root widget is the one
     * that will actually be rendered (if that widget is itself renderable - if not,
     * then its root widget will be rendered instead, and so on).
     *
     * @return bool
     */
    public function isRenderable();
}
