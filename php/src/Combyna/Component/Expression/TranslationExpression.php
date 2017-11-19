<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class TranslationExpression
 *
 * References a translation key for a message to be displayed by the app
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TranslationExpression extends AbstractExpression
{
    const TYPE = 'translation';

    /**
     * @var ExpressionBagInterface
     */
    private $argumentExpressionBag;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $translationKey;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param string $translationKey
     * @param ExpressionBagInterface $argumentExpressionBag
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        $translationKey,
        ExpressionBagInterface $argumentExpressionBag
    ) {
        $this->argumentExpressionBag = $argumentExpressionBag;
        $this->expressionFactory = $expressionFactory;
        $this->translationKey = $translationKey;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $argumentStaticBag = $this->argumentExpressionBag->toStaticBag($evaluationContext);

        $translatedMessage = $evaluationContext->translate(
            $this->translationKey,
            $argumentStaticBag->toNativeArray()
        );

        return $this->expressionFactory->createTextExpression($translatedMessage);
    }
}
