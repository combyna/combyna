<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;
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
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ValidationFactoryInterface $validationFactory
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ValidationFactoryInterface $validationFactory
    ) {
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->validationFactory = $validationFactory;
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
        return new FixedStaticBagModel($this->validationFactory, $staticDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedStaticDefinition(
        $name,
        TypeInterface $type,
        StaticInterface $defaultStatic = null
    ) {
        return new FixedStaticDefinition($this->validationFactory, $name, $type, $defaultStatic);
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
