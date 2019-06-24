<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Expression\Evaluation;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Bag\StaticProviderBagInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContextFactory;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Class BagEvaluationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BagEvaluationContextFactory extends AbstractEvaluationContextFactory implements BagEvaluationContextFactoryInterface
{
    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param EvaluationContextFactoryInterface $parentContextFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(
        EvaluationContextFactoryInterface $parentContextFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory
    ) {
        parent::__construct($parentContextFactory);

        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNativeBagCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        array $nativeValues
    ) {
        return new NativeBagCoercionEvaluationContext(
            $this,
            $parentContext,
            $this->staticExpressionFactory,
            $bagFactory,
            $fixedStaticBagModel,
            $nativeValues
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticBagCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticBagInterface $staticBag
    ) {
        return new StaticBagCoercionEvaluationContext(
            $this,
            $parentContext,
            $fixedStaticBagModel,
            $staticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticProviderBagInterface $staticProviderBag,
        StaticInterface $static = null
    ) {
        return new StaticCoercionEvaluationContext(
            $this,
            $parentContext,
            $fixedStaticBagModel,
            $staticProviderBag,
            $static
        );
    }
}
