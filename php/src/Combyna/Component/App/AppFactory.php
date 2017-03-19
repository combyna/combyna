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
use Combyna\Component\Ui\ViewFactoryInterface;

/**
 * Class App
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppFactory implements AppFactoryInterface
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
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param ViewFactoryInterface $viewFactory
     * @param ExpressionFactoryInterface $expressionFactory
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        ViewFactoryInterface $viewFactory,
        ExpressionFactoryInterface $expressionFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionFactory = $expressionFactory;
        $this->viewFactory = $viewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        EvaluationContextInterface $rootEvaluationContext,
        ViewCollectionInterface $viewCollection
    ) {
        return new App($this->bagFactory, $this->expressionFactory, $viewCollection, $rootEvaluationContext);
    }
}
