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
 * Class ProgramFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ProgramFactory implements ProgramFactoryInterface
{
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
            $rootEvaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createResourceRepository(
        EnvironmentInterface $environment,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    ) {
        return new ResourceRepository($environment, $signalDefinitionRepository);
    }
}
