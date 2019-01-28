<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\Evaluation\EventEvaluationContext;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Signal\Evaluation\SignalEvaluationContext;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Type\TypeInterface;
use InvalidArgumentException;

/**
 * Interface EvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EvaluationContextInterface
{
    /**
     * Calls a function and returns its static result. The return type must be passed in
     * as it can be different depending on the types of the arguments provided
     * (eg. if a custom TypeDeterminer is used for a parameter's type)
     *
     * @param string $libraryName
     * @param string $functionName
     * @param StaticBagInterface $argumentStaticBag
     * @param TypeInterface $returnType
     * @return StaticInterface
     */
    public function callFunction(
        $libraryName,
        $functionName,
        StaticBagInterface $argumentStaticBag,
        TypeInterface $returnType
    );

    /**
     * Creates a new AssuredEvaluationContext as a child of the current one,
     * with the provided static bag exposed inside it as assured statics
     *
     * @param StaticBagInterface $assuredStaticBag
     * @return AssuredEvaluationContext
     */
    public function createSubAssuredContext(StaticBagInterface $assuredStaticBag);

    /**
     * Creates a new EventEvaluationContext as a child of the current one,
     * with the specified event as the one to fetch event payload data (eg. the X/Y coords of a click) from
     *
     * @param EventInterface $event
     * @return EventEvaluationContext
     */
    public function createSubEventEvaluationContext(EventInterface $event);

    /**
     * Creates a new ExpressionEvaluationContext as a child of the current one,
     * with the specified expression as the one to use as "current"
     *
     * @param ExpressionInterface $expression
     * @return ExpressionEvaluationContext
     */
    public function createSubExpressionContext(ExpressionInterface $expression);

    /**
     * Creates a new ScopeEvaluationContext as a child of the current one,
     * with the provided static bag exposed inside it as variables
     *
     * @param StaticBagInterface $variableStaticBag
     * @return EvaluationContextInterface
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag);

    /**
     * Creates a new SignalEvaluationContext as a child of the current one,
     * with the specified signal as the one to fetch signal payload data from
     *
     * @param SignalInterface $signal
     * @return SignalEvaluationContext
     */
    public function createSubSignalEvaluationContext(SignalInterface $signal);

    /**
     * Fetches the specified assured static value
     *
     * @param string $assuredStaticName
     * @return StaticInterface
     */
    public function getAssuredStatic($assuredStaticName);

    /**
     * Descends to the node that sets the specified capture and evaluates it to a static,
     * if found, otherwise returns null
     *
     * @param string $captureName
     * @return StaticInterface|null
     */
    public function getCaptureLeafwise($captureName);

    /**
     * Ascends the ACT to find the node that defines the specified capture,
     * then descends to the node that sets and evaluates it to a static
     *
     * @param string $captureName
     * @return StaticInterface
     */
    public function getCaptureRootwise($captureName);

    /**
     * Fetches the environment
     *
     * @return EnvironmentInterface
     */
    public function getEnvironment();

    /**
     * Fetches the specified static from the current event's payload
     *
     * @param string $staticName
     * @return StaticInterface
     */
    public function getEventPayloadStatic($staticName);

    /**
     * Fetches the specified static from the current signal's payload
     *
     * @param string $staticName
     * @return StaticInterface
     */
    public function getSignalPayloadStatic($staticName);

    /**
     * Fetches the value of the specified store slot static
     *
     * @param string $name
     * @return StaticInterface
     */
    public function getStoreSlotStatic($name);

    /**
     * Fetches the value of the specified variable
     *
     * @param string $variableName
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the specified variable is not defined in this or a parent
     */
    public function getVariable($variableName);

    /**
     * Evaluates and then returns the value of the specified compound widget attribute
     *
     * @param string $attributeName
     * @return StaticInterface
     */
    public function getWidgetAttribute($attributeName);

    /**
     * Evaluates and then returns the value of the specified widget value
     *
     * @param string $valueName
     * @return StaticInterface
     */
    public function getWidgetValue($valueName);

    /**
     * Makes the specified query on a view store, returning its static result
     *
     * @param string $queryName
     * @param StaticBagInterface $argumentStaticBag
     * @return StaticInterface
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag);

    /**
     * Translates a translation key and optional parameters to a message
     *
     * @param string $translationKey
     * @param array $parameters
     * @return string
     */
    public function translate($translationKey, array $parameters = []);
}
