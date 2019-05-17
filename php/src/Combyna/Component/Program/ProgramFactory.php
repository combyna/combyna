<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Class ProgramFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProgramFactory implements ProgramFactoryInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     */
    public function __construct(UiEvaluationContextFactoryInterface $uiEvaluationContextFactory)
    {
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createProgram(
        EnvironmentInterface $environment,
        ResourceRepositoryInterface $resourceRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        return new Program(
            $environment,
            $resourceRepository,
            $pageViewCollection,
            $overlayViewCollection,
            $rootEvaluationContext,
            $this->uiEvaluationContextFactory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createResourceRepository(EnvironmentInterface $environment)
    {
        return new ResourceRepository($environment);
    }
}
