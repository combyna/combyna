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
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class NativeBagCoercionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeBagCoercionEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $fixedStaticBagModel;

    /**
     * @var array
     */
    private $nativeValues;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param BagFactoryInterface $bagFactory
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param array $nativeValues
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        array $nativeValues
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->bagFactory = $bagFactory;
        $this->fixedStaticBagModel = $fixedStaticBagModel;
        $this->nativeValues = $nativeValues;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiblingBagStatic($staticName)
    {
        if (!$this->fixedStaticBagModel->definesStatic($staticName) ||
            !array_key_exists($staticName, $this->nativeValues)
        ) {
            throw new NotFoundException(sprintf(
                'Bag does not contain static "%s", only: "%s"',
                $staticName,
                implode('", "', $this->fixedStaticBagModel->getStaticDefinitionNames())
            ));
        }

        return $this->fixedStaticBagModel->getStaticDefinitionByName($staticName, new NullActNodeAdopter())
            ->getStaticType()
            ->coerceNative(
                $this->nativeValues[$staticName],
                $this->staticExpressionFactory,
                $this->bagFactory,
                $this
            );
    }
}
