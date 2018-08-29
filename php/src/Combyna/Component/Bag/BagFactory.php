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
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(StaticExpressionFactoryInterface $staticExpressionFactory)
    {
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * @inheritdoc}
     */
    public function coerceStaticBag(array $natives)
    {
        $statics = [];

        foreach ($natives as $name => $value) {
            $statics[$name] = $this->staticExpressionFactory->coerce($value);
        }

        return $this->createStaticBag($statics);
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
        return new FixedStaticBagModel($this, $staticDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedStaticDefinition(
        $name,
        TypeInterface $type,
        ExpressionInterface $defaultExpression = null
    ) {
        return new FixedStaticDefinition($name, $type, $defaultExpression);
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
