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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class TextWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidget implements TextWidgetInterface
{
    const DEFINITION = 'text';

    /**
     * @var ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetInterface
     */
    private $parentWidget;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionInterface
     */
    private $textExpression;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface $parentWidget
     * @param string|int $name
     * @param ExpressionInterface $textExpression
     * @param UiStateFactoryInterface $uiStateFactory
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $name,
        ExpressionInterface $textExpression,
        UiStateFactoryInterface $uiStateFactory,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
        $this->textExpression = $textExpression;
        $this->uiStateFactory = $uiStateFactory;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        WidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof TextWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    TextWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createTextWidgetEvaluationContext($parentContext, $this, $widgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        throw new LogicException('TextWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        $name,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        // Make any capture-definitions (that this widget defines) and any capture-sets
        // that this widget or any child widget makes available to descendants
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        $textStatic = $this->textExpression->toStatic($subEvaluationContext);

        return $this->uiStateFactory->createTextWidgetState($name, $this, $textStatic->toNative());
    }

    /**
     * {@inheritdoc}
     */
    public function descendantsSetCaptureInclusive($captureName)
    {
        // TextWidgets cannot have descendants, but can set captures themselves
        return $this->captureExpressionBag->hasExpression($captureName);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    ) {
        throw new LogicException('TextWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'TextWidgets cannot have attributes, so attribute "%s" cannot be fetched',
            $attributeName
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureExpressionBag()
    {
        return $this->captureExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureStaticBagModel()
    {
        return $this->captureStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLibraryName()
    {
        return self::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return self::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantByPath(array $names)
    {
        throw new LogicException('TextWidgets cannot have descendants');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return array_merge($this->parentWidget->getPath(), [$this->name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag($tag)
    {
        return array_key_exists($tag, $this->tags) && $this->tags[$tag] === true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return true; // TextWidgets cannot be resolved further, so they are always renderable
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if (!$oldState instanceof TextWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    TextWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Make any capture-definitions (that this widget defines) and any capture-sets
        // that this widget or any child widget makes available to descendants
        $subEvaluationContext = $this->createEvaluationContext($evaluationContext, $evaluationContextFactory);

        $textStatic = $this->textExpression->toStatic($subEvaluationContext);

        return $oldState->with($textStatic->toNative());
    }
}
