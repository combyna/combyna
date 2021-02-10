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

use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use LogicException;

/**
 * Class CompoundWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetEvaluationContext extends AbstractEvaluationContext implements CompoundWidgetEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @var CompoundWidgetDefinition
     */
    private $widgetDefinition;

    /**
     * @var DefinedCompoundWidgetStateInterface|null
     */
    private $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param CompoundWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->widget = $widget;
        $this->widgetDefinition = $widgetDefinition;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        return $this->evaluationContextFactory->createWidgetStoreEvaluationContext($this, $storeState);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureLeafwise($captureName)
    {
        if ($this->widget->getCaptureExpressionBag()->hasExpression($captureName)) {
            $definitionSubEvaluationContext = $this->evaluationContextFactory
                ->createCompoundWidgetDefinitionEvaluationContext(
                    $this,
                    $this->widgetDefinition,
                    $this->widget,
                    $this->widgetState
                );

            // This widget sets the capture - evaluate and return. Evaluate in the context
            // of the widget definition, so that the expression has access to widget values
            return $this->widget->getCaptureExpressionBag()
                ->getExpression($captureName)
                ->toStatic($definitionSubEvaluationContext);
        }

        foreach ($this->widget->getChildWidgets() as $childWidget) {
            // Fetch the child widget state if it has been created already
            $childWidgetState = $this->widgetState ?
                $this->widgetState->getChildState($childWidget->getName()) :
                null;

            $childWidgetEvaluationContext = $childWidget
                ->createEvaluationContext(
                    $this,
                    $this->evaluationContextFactory,
                    $childWidgetState
                );

            $captureStatic = $childWidgetEvaluationContext->getCaptureLeafwise($captureName);

            if ($captureStatic !== null) {
                return $captureStatic;
            }
        }

        // No descendants set the capture
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureRootwise($captureName)
    {
        if (!$this->widget->getCaptureStaticBagModel()->definesStatic($captureName)) {
            // This widget does not define the capture - it should be defined by an ancestor further up
            return $this->parentContext->getCaptureRootwise($captureName);
        }

        // This widget defines the capture - it should be set by a descendant (or itself)
        $captureStatic = $this->getCaptureLeafwise($captureName);

        if ($captureStatic === null) {
            // If this widget defines the capture, a descendant _must_ set it
            throw new LogicException(
                sprintf(
                    'Capture "%s" was not set',
                    $captureName
                )
            );
        }

        return $captureStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildOfCurrentCompoundWidget($childName)
    {
        return $this->parentContext->getChildOfCurrentCompoundWidget($childName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return array_merge($this->parentContext->getPath(), [$this->widget->getName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetState()
    {
        return $this->widgetState;
    }
}
