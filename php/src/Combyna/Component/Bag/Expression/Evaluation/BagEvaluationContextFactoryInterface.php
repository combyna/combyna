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
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface BagEvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BagEvaluationContextFactoryInterface extends EvaluationContextFactoryInterface
{
    /**
     * Creates a NativeBagCoercionEvaluationContext
     *
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextInterface $parentContext
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param array $nativeValues
     * @return NativeBagCoercionEvaluationContext
     */
    public function createNativeBagCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        array $nativeValues
    );

    /**
     * Creates a StaticBagCoercionEvaluationContext
     *
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextInterface $parentContext
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param StaticBagInterface $staticBag
     * @return StaticBagCoercionEvaluationContext
     */
    public function createStaticBagCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticBagInterface $staticBag
    );

    /**
     * Creates a StaticCoercionEvaluationContext
     *
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextInterface $parentContext
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param StaticProviderBagInterface $staticProviderBag
     * @param StaticInterface|null $static
     * @return StaticCoercionEvaluationContext
     */
    public function createStaticCoercionEvaluationContext(
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticProviderBagInterface $staticProviderBag,
        StaticInterface $static = null
    );
}
