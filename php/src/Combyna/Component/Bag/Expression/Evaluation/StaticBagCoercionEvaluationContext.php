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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Validator\Config\Act\NullActNodeAdopter;

/**
 * Class StaticBagCoercionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticBagCoercionEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $fixedStaticBagModel;

    /**
     * @var StaticBagInterface
     */
    private $staticBag;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param FixedStaticBagModelInterface $fixedStaticBagModel
     * @param StaticBagInterface $staticBag
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        FixedStaticBagModelInterface $fixedStaticBagModel,
        StaticBagInterface $staticBag
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->fixedStaticBagModel = $fixedStaticBagModel;
        $this->staticBag = $staticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiblingBagStatic($staticName)
    {
        if (!$this->fixedStaticBagModel->definesStatic($staticName) ||
            !$this->staticBag->hasStatic($staticName)
        ) {
            throw new NotFoundException(sprintf(
                'Bag does not contain static "%s", only: "%s"',
                $staticName,
                implode('", "', $this->fixedStaticBagModel->getStaticDefinitionNames())
            ));
        }

        return $this->fixedStaticBagModel->getStaticDefinitionByName($staticName, new NullActNodeAdopter())
            ->getStaticType()
            ->coerceStatic(
                $this->staticBag->getStatic($staticName),
                $this
            );
    }
}
