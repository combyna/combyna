<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionBagInterface
     */
    private $parameterExpressionBag;

    /**
     * @var string
     */
    private $translationKey;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param string $translationKey
     * @param ExpressionBagInterface $parameterExpressionBag
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        $translationKey,
        ExpressionBagInterface $parameterExpressionBag
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->parameterExpressionBag = $parameterExpressionBag;
        $this->translationKey = $translationKey;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $parameterStaticBag = $this->parameterExpressionBag->toStaticBag($subEvaluationContext);

        $translatedMessage = $subEvaluationContext->translate(
            $this->translationKey,
            $parameterStaticBag->toNativeArray()
        );

        return $this->expressionFactory->createTextExpression($translatedMessage);
    }
}
