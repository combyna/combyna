<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Ui\ViewCollectionInterface;

/**
 * Class App
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class App implements AppInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $rootEvaluationContext;

    /**
     * @var ViewCollectionInterface
     */
    private $viewCollection;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ViewCollectionInterface $viewCollection
     * @param EvaluationContextInterface $rootEvaluationContext
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        ExpressionFactoryInterface $expressionFactory,
        ViewCollectionInterface $viewCollection,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionFactory = $expressionFactory;
        $this->rootEvaluationContext = $rootEvaluationContext;
        $this->viewCollection = $viewCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView($viewName, array $viewAttributes = [])
    {
        $viewAttributeStatics = [];

        foreach ($viewAttributes as $name => $value) {
            $viewAttributeStatics[$name] = $this->expressionFactory->coerce($value);
        }

        $viewAttributeBag = $this->bagFactory->createStaticBag($viewAttributeStatics);

        return $this->viewCollection->renderView($viewName, $viewAttributeBag, $this->rootEvaluationContext);
    }
}
