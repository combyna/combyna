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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use LogicException;

/**
 * Class ChildReferenceWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidget implements ChildReferenceWidgetInterface
{
    const DEFINITION = 'child';

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var string
     */
    private $childName;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetInterface|null
     */
    private $parentWidget;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface|null $parentWidget
     * @param string|int $name
     * @param string $childName
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $uiStateFactory
     * @param FixedStaticBagModelInterface $captureStaticBagModel
     * @param ExpressionBagInterface $captureExpressionBag
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     */
    public function __construct(
        WidgetInterface $parentWidget = null,
        $name,
        $childName,
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $uiStateFactory,
        FixedStaticBagModelInterface $captureStaticBagModel,
        ExpressionBagInterface $captureExpressionBag,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        $this->bagFactory = $bagFactory;
        $this->captureExpressionBag = $captureExpressionBag;
        $this->captureStaticBagModel = $captureStaticBagModel;
        $this->childName = $childName;
        $this->name = $name;
        $this->parentWidget = $parentWidget;
        $this->tags = $tags;
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
        if ($widgetState && !$widgetState instanceof ChildReferenceWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    ChildReferenceWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $evaluationContextFactory->createChildReferenceWidgetEvaluationContext(
            $parentContext,
            $this,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent(
        $libraryName,
        $eventName,
        array $payloadNatives,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        throw new LogicException('ChildReferenceWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        $name,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        $compoundWidgetDefinitionContext = $evaluationContext->getCompoundWidgetDefinitionContext();

        $childWidget = $compoundWidgetDefinitionContext->getChildWidget($this->childName);

        return $this->uiStateFactory->createChildReferenceWidgetState(
            $name,
            $this,
            // Each embedded instance of a compound widget's child will get a separate state. This allows
            // eg. a self-incrementing button to maintain a separate counter for each place it is embedded.
            $childWidget->createInitialState(
                $name,
                // To avoid infinite recursion, go above the compound widget context
                // so that we don't attempt to fetch the referenced child widget from the same one again
                $compoundWidgetDefinitionContext->getParentContext(),
                $evaluationContextFactory
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function descendantsSetCaptureInclusive($captureName)
    {
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
        throw new LogicException('ChildReferenceWidgets cannot handle events');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName, ViewEvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(sprintf(
            'ChildReferenceWidgets cannot have attributes, so attribute "%s" cannot be fetched',
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
    public function getChildName()
    {
        return $this->childName;
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
        throw new LogicException('ChildReferenceWidgets cannot have descendants');
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
    public function getParentWidget()
    {
        return $this->parentWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->parentWidget !== null ?
            array_merge($this->parentWidget->getPath(), [$this->name]) :
            [$this->name];
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
        return true; // ChildReferenceWidgets cannot be resolved further, so they are always renderable
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateState(
        WidgetStateInterface $oldState,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if (!$oldState instanceof ChildReferenceWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    ChildReferenceWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        $compoundWidgetDefinitionContext = $evaluationContext->getCompoundWidgetDefinitionContext();

        $childWidget = $compoundWidgetDefinitionContext->getChildWidget($this->childName);

        return $oldState->with(
            $childWidget->reevaluateState(
                $oldState->getChildState('child'),
                // To avoid infinite recursion, go above the compound widget context
                // so that we don't attempt to fetch the referenced child widget from the same one again
                $compoundWidgetDefinitionContext->getParentContext(),
                $evaluationContextFactory
            )
        );
    }
}
