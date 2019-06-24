<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Bag\Expression\Evaluation\BagEvaluationContextFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class BagFactory
 *
 * Creates objects related to bags and lists
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BagFactory implements BagFactoryInterface
{
    /**
     * @var BagEvaluationContextFactoryInterface
     */
    private $bagEvaluationContextFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param BagEvaluationContextFactoryInterface $bagEvaluationContextFactory
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagEvaluationContextFactoryInterface $bagEvaluationContextFactory
    ) {
        $this->bagEvaluationContextFactory = $bagEvaluationContextFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionBag(array $expressions)
    {
        return new ExpressionBag($this, $expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionList(array $expressions)
    {
        return new ExpressionList($this, $expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedStaticBagModel(array $staticDefinitions)
    {
        return new FixedStaticBagModel(
            $this,
            $this->staticExpressionFactory,
            $this->bagEvaluationContextFactory,
            $staticDefinitions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedStaticDefinition(
        $name,
        TypeInterface $staticType,
        ExpressionInterface $defaultExpression = null
    ) {
        return new FixedStaticDefinition($name, $staticType, $defaultExpression);
    }

    /**
     * {@inheritdoc}
     */
    public function createMutableStaticBag(array $statics = [])
    {
        return new MutableStaticBag($statics);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticBag(array $statics)
    {
        return new StaticBag($statics);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticList(array $statics)
    {
        return new StaticList($this, $this->staticExpressionFactory, $statics);
    }
}
