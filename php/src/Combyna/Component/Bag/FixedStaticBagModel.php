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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use LogicException;

/**
 * Class FixedStaticBagModel
 *
 * Defines the statics and their types that a bag may store internally
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModel implements FixedStaticBagModelInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var FixedStaticDefinition[]
     */
    private $staticDefinitions = [];

    /**
     * @param BagFactoryInterface $bagFactory
     * @param FixedStaticDefinition[] $staticDefinitions
     */
    public function __construct(BagFactoryInterface $bagFactory, array $staticDefinitions)
    {
        // Index definitions by name to simplify lookups
        foreach ($staticDefinitions as $staticDefinition) {
            $this->staticDefinitions[$staticDefinition->getName()] = $staticDefinition;
        }

        $this->bagFactory = $bagFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStatic($name, StaticInterface $value)
    {
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStaticBag(StaticBagInterface $staticBag)
    {
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function createBag(
        ExpressionBagInterface $expressionBag,
        EvaluationContextInterface $explicitEvaluationContext,
        EvaluationContextInterface $defaultsEvaluationContext
    ) {
        $statics = [];

        foreach ($this->staticDefinitions as $staticDefinition) {
            $statics[$staticDefinition->getName()] = $expressionBag->hasExpression($staticDefinition->getName()) ?
                $expressionBag->getExpression($staticDefinition->getName())->toStatic($explicitEvaluationContext) :
                $staticDefinition->getDefaultStatic($defaultsEvaluationContext);
        }

        $staticBag = $this->bagFactory->createStaticBag($statics);

        $this->assertValidStaticBag($staticBag);

        return $staticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function createBagWithCallback(callable $staticFetcher)
    {
        $statics = [];

        foreach ($this->staticDefinitions as $staticDefinition) {
            $statics[$staticDefinition->getName()] = $staticFetcher($staticDefinition->getName());
        }

        $staticBag = $this->bagFactory->createStaticBag($statics);

        $this->assertValidStaticBag($staticBag);

        return $staticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultStaticBag(EvaluationContextInterface $evaluationContext)
    {
        $statics = [];

        foreach ($this->staticDefinitions as $staticDefinition) {
            $statics[$staticDefinition->getName()] = $staticDefinition->getDefaultStatic($evaluationContext);
        }

        return $this->bagFactory->createStaticBag($statics);
    }

    /**
     * {@inheritdoc}
     */
    public function definesStatic($name)
    {
        return array_key_exists($name, $this->staticDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultStatic($name, EvaluationContextInterface $evaluationContext)
    {
        if (!$this->definesStatic($name)) {
            throw new LogicException(
                sprintf(
                    'Bag model does not define static %s',
                    $name
                )
            );
        }

        return $this->staticDefinitions[$name]->getDefaultStatic($evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType($name)
    {
        if (!$this->definesStatic($name)) {
            throw new LogicException(
                sprintf(
                    'Bag model does not define static %s',
                    $name
                )
            );
        }

        return $this->staticDefinitions[$name]->getStaticType();
    }
}
