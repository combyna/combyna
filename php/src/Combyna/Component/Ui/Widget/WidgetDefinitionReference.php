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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Evaluation\DefinedWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;

/**
 * Class WidgetDefinitionReference
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionReference implements WidgetDefinitionReferenceInterface
{
    /**
     * @var WidgetDefinitionInterface|null
     */
    private $definition = null;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param ResourceRepositoryInterface $resourceRepository
     * @param string $libraryName
     * @param string $widgetDefinitionName
     */
    public function __construct(ResourceRepositoryInterface $resourceRepository, $libraryName, $widgetDefinitionName)
    {
        $this->libraryName = $libraryName;
        $this->resourceRepository = $resourceRepository;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        return $this->getDefinition()->assertValidAttributeStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinitionEvaluationContextForWidget(
        DefinedWidgetEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState = null
    ) {
        return $this->getDefinition()->createDefinitionEvaluationContextForWidget($parentContext, $widget, $widgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContextForWidget(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState = null
    ) {
        return $this->getDefinition()->createEvaluationContextForWidget($parentContext, $widget, $widgetState);
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
        return $this->getDefinition()->createEvent($libraryName, $eventName, $payloadNatives, $evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialStateForWidget(
        $name,
        DefinedWidgetInterface $widget,
        ExpressionBagInterface $attributeExpressionBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        return $this->getDefinition()->createInitialStateForWidget(
            $name,
            $widget,
            $attributeExpressionBag,
            $evaluationContext,
            $evaluationContextFactory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(
        $name,
        ExpressionBagInterface $attributeExpressionBag,
        EvaluationContextInterface $evaluationContext
    ) {
        return $this->getDefinition()->getAttribute($name, $attributeExpressionBag, $evaluationContext);
    }

    /**
     * Fetches the definition being referenced
     *
     * @return WidgetDefinitionInterface
     * @throws WidgetDefinitionNotSupportedException
     */
    private function getDefinition()
    {
        if ($this->definition === null) {
            $this->definition = $this->resourceRepository->getWidgetDefinitionByName(
                $this->libraryName,
                $this->widgetDefinitionName
            );
        }

        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValue(
        $valueName,
        array $widgetStatePath,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        return $this->getDefinition()->getWidgetValue($valueName, $widgetStatePath, $evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return $this->getDefinition()->isRenderable();
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateStateForWidget(
        DefinedWidgetStateInterface $oldState,
        DefinedWidgetInterface $widget,
        ExpressionBagInterface $attributeExpressionBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        return $this->getDefinition()->reevaluateStateForWidget(
            $oldState,
            $widget,
            $attributeExpressionBag,
            $evaluationContext,
            $evaluationContextFactory
        );
    }
}
