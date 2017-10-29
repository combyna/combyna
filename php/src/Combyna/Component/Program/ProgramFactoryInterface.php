<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Interface ProgramFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProgramFactoryInterface
{
    /**
     * Creates a new Program
     *
     * @param EnvironmentInterface $environment
     * @param ResourceRepositoryInterface $resourceRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return ProgramInterface
     */
    public function createProgram(
        EnvironmentInterface $environment,
        ResourceRepositoryInterface $resourceRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EvaluationContextInterface $rootEvaluationContext
    );

    /**
     * Creates a new ResourceRepository
     *
     * @param EnvironmentInterface $environment
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @return ResourceRepositoryInterface
     */
    public function createResourceRepository(
        EnvironmentInterface $environment,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    );
}
