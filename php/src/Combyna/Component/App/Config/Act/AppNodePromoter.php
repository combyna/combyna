<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\App\AppFactoryInterface;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\EnvironmentPromoter;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Ui\Config\Act\UiNodePromoter;

/**
 * Class AppNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppNodePromoter
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    /**
     * @var EnvironmentPromoter
     */
    private $environmentPromoter;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var UiNodePromoter
     */
    private $uiNodePromoter;

    /**
     * @param AppFactoryInterface $appFactory
     * @param EnvironmentPromoter $environmentPromoter
     * @param UiNodePromoter $uiNodePromoter
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     */
    public function __construct(
        AppFactoryInterface $appFactory,
        EnvironmentPromoter $environmentPromoter,
        UiNodePromoter $uiNodePromoter,
        EvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        $this->appFactory = $appFactory;
        $this->environmentPromoter = $environmentPromoter;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->uiNodePromoter = $uiNodePromoter;
    }

    /**
     * Promotes an AppNode to an App
     *
     * @param AppNode $appNode
     * @param EnvironmentNode $environmentNode
     * @return AppInterface
     */
    public function promoteApp(AppNode $appNode, EnvironmentNode $environmentNode)
    {
        $environment = $this->environmentPromoter->promoteEnvironment($environmentNode);
        
        $rootEvaluationContext = $this->evaluationContextFactory->createRootContext($environment);

        return $this->appFactory->create(
            $rootEvaluationContext,
            $this->uiNodePromoter->promoteViewCollection($appNode->getViewCollection(), $environment)
        );
    }
}
