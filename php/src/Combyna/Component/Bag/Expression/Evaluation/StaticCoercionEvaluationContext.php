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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticProviderBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class StaticCoercionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticCoercionEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $fixedStaticBagModel;

    /**
     * @var StaticInterface|null
     */
    private $static;

    /**
     * @var StaticProviderBagInterface
     */
    private $staticSourceBag;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param StaticProviderBagInterface $staticProviderBag
     * @param StaticInterface|null $static
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticProviderBagInterface $staticProviderBag,
        StaticInterface $static = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->fixedStaticBagModel = $fixedStaticBagModel;
        $this->static = $static;
        $this->staticSourceBag = $staticProviderBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiblingBagStatic($staticName)
    {
        if (!$this->fixedStaticBagModel->definesStatic($staticName) ||
            !$this->staticSourceBag->providesStatic($staticName)
        ) {
            throw new NotFoundException(sprintf(
                'Bag does not contain expression "%s", only: "%s"',
                $staticName,
                implode('", "', $this->fixedStaticBagModel->getStaticDefinitionNames())
            ));
        }

        return $this->fixedStaticBagModel->getStaticDefinitionByName($staticName, new NullActNodeAdopter())
            ->getStaticType()
            ->coerceStatic(
                $this->staticSourceBag->evaluateStatic($staticName, $this),
                $this
            );
    }
}
