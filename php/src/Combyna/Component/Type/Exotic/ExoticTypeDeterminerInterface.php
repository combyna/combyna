<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\Exotic\Determination\TypeDeterminationInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface ExoticTypeDeterminerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExoticTypeDeterminerInterface
{
    /**
     * Coerces a native value for this type to a static, if possible
     *
     * @param mixed $nativeValue
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     * @throws IncompatibleNativeForCoercionException
     */
    public function coerceNative(
        $nativeValue,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $evaluationContext
    );

    /**
     * Coerces a potentially "incomplete" static for this type to a "complete" one, if possible
     *
     * @param StaticInterface $static
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext);

    /**
     * Determines the type that this exotic type resolves to
     *
     * @param TypeInterface $destinationType
     * @param TypeInterface $candidateType
     * @return TypeDeterminationInterface
     */
    public function determine(TypeInterface $destinationType, TypeInterface $candidateType);

    /**
     * Fetches the unique exotic type determiner name
     *
     * @return string
     */
    public function getName();
}
