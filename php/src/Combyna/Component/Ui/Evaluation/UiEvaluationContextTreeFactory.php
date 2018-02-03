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

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\State\StatePathInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use InvalidArgumentException;

/**
 * Class UiEvaluationContextTreeFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiEvaluationContextTreeFactory implements UiEvaluationContextTreeFactoryInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     */
    public function __construct(UiEvaluationContextFactoryInterface $evaluationContextFactory)
    {
        $this->evaluationContextFactory = $evaluationContextFactory;
    }

    /**
     * Creates an EvaluationContext for a given state
     *
     * @param EnvironmentInterface $environment
     * @param StatePathInterface $statePath
     * @param ProgramInterface $program
     * @param EvaluationContextInterface|null $parentContext
     * @return EvaluationContextInterface
     */
    private function createContextFromStatePath(
        StatePathInterface $statePath,
        ProgramInterface $program,
        EnvironmentInterface $environment,
        EvaluationContextInterface $parentContext = null
    ) {
        $map = $this->evaluationContextFactory->getStateTypeToContextFactoryMap();

        if (!array_key_exists($statePath->getEndStateType(), $map)) {
            throw new InvalidArgumentException(
                sprintf(
                    'No context factory for state type "%s"',
                    $statePath->getEndStateType()
                )
            );
        }

        return $map[$statePath->getEndStateType()](
            $parentContext,
            $statePath,
            $statePath->getEndState(),
            $program,
            $environment
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPageViewEvaluationContextTree(
        PageViewStateInterface $pageViewState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $context = $this->evaluationContextFactory->createPageViewEvaluationContextFromPageViewState(
            $program->getRootEvaluationContext(),
            $pageViewState,
            $program,
            $environment
        );

        if (!($context instanceof ViewEvaluationContextInterface)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected the context ' .
                    'to be a (Root)ViewEvaluationContext, but it was a "%s"',
                    get_class($context)
                )
            );
        }

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetEvaluationContextTree(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $parentContext = $program->getRootEvaluationContext();

        foreach ($widgetStatePath->getSubStatePaths() as $statePath) {
            $parentContext = $this->createContextFromStatePath($statePath, $program, $environment, $parentContext);
        }

        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        if (!($parentContext instanceof ViewEvaluationContextInterface)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected the parent context of the WidgetEvaluationContext ' .
                    'to be a (Root)ViewEvaluationContext, but it was a "%s"',
                    get_class($parentContext)
                )
            );
        }

        return $widget->createEvaluationContext($parentContext, $this->evaluationContextFactory);
    }
}
