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

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticBagModelInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
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
     * @var FixedStaticDefinitionInterface[]
     */
    private $staticDefinitions = [];

    /**
     * @param BagFactoryInterface $bagFactory
     * @param FixedStaticDefinitionInterface[] $staticDefinitions
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
    public function allowsOtherModel(DeterminedFixedStaticBagModelInterface $otherModel)
    {
        // Check there are no required statics in this model that are missing from the other one
        // (a static that is required in _this_ model can be optional in the other,
        // as we'll be able to use the default expression specified in the other model if needed)
        foreach ($this->staticDefinitions as $definitionName => $definition) {
            if (!$otherModel->definesStatic($definitionName) && $definition->isRequired()) {
                return false;
            }
        }

        // Check there are no statics in the other model that aren't part of this one
        foreach ($otherModel->getStaticDefinitionNames() as $definitionName) {
            if (!$this->definesStatic($definitionName)) {
                return false;
            }
        }

        // Check all statics in the other model are allowed
        // by their corresponding static definitions in this one
        foreach ($otherModel->getStaticDefinitions() as $theirDefinition) {
            $ourDefinition = $this->staticDefinitions[$theirDefinition->getName()];

            if (!$ourDefinition->allowsStaticDefinition($theirDefinition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticBag(StaticBagInterface $staticBag)
    {
        // Check there are no required statics in this model that are missing from the static bag
        foreach ($this->staticDefinitions as $staticName => $definition) {
            if (!$staticBag->hasStatic($staticName) && $definition->isRequired()) {
                return false;
            }
        }

        // Check there are no statics in the static bag that aren't part of this one
        foreach ($staticBag->getStaticNames() as $staticName) {
            if (!$this->definesStatic($staticName)) {
                return false;
            }
        }

        // Check all statics in the bag are allowed
        // by their corresponding static definitions in this model
        foreach ($staticBag->getStaticNames() as $staticName) {
            $staticDefinition = $this->staticDefinitions[$staticName];

            if (!$staticDefinition->allowsStatic($staticBag->getStatic($staticName))) {
                return false;
            }
        }

        return true;
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
    public function coerceStatic($name, EvaluationContextInterface $evaluationContext, StaticInterface $static = null)
    {
        if (!$this->definesStatic($name)) {
            throw new LogicException(sprintf('Bag model does not define static %s', $name));
        }

        return $this->staticDefinitions[$name]->coerceStatic($evaluationContext, $static);
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStaticBag(StaticBagInterface $staticBag, EvaluationContextInterface $evaluationContext)
    {
        $coercedStatics = [];

        foreach ($this->staticDefinitions as $name => $staticDefinition) {
            $coercedStatics[$name] = $staticBag->hasStatic($name) ?
                $staticDefinition->coerceStatic($evaluationContext, $staticBag->getStatic($name)) :
                $staticDefinition->getDefaultStatic($evaluationContext);
        }

        return $staticBag->withStatics($coercedStatics);
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
            $statics[$staticDefinition->getName()] = $this->coerceStatic(
                $staticDefinition->getName(),
                $defaultsEvaluationContext,
                $expressionBag->hasExpression($staticDefinition->getName()) ?
                    $expressionBag->getExpression($staticDefinition->getName())->toStatic($explicitEvaluationContext) :
                    null
            );
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
    public function getStaticDefinitionByName($definitionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        if (!$this->definesStatic($definitionName)) {
            throw new LogicException(
                sprintf(
                    'Bag model does not define static "%s"',
                    $definitionName
                )
            );
        }

        return $this->staticDefinitions[$definitionName];
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitionNames()
    {
        return array_keys($this->staticDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticDefinitions()
    {
        return $this->staticDefinitions;
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

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $staticDefinitionSummaries = [];

        foreach ($this->staticDefinitions as $staticDefinition) {
            $staticDefinitionSummaries[] = sprintf(
                '%s: %s',
                $staticDefinition->getName(),
                $staticDefinition->getStaticTypeSummary()
            );
        }

        return sprintf('{%s}', implode(', ', $staticDefinitionSummaries));
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryWithValue()
    {
        $staticDefinitionSummaries = [];

        foreach ($this->staticDefinitions as $staticDefinition) {
            $staticDefinitionSummaries[] = sprintf(
                '%s: %s',
                $staticDefinition->getName(),
                $staticDefinition->getStaticTypeSummaryWithValue() // As above, but including value info
            );
        }

        return sprintf('{%s}', implode(', ', $staticDefinitionSummaries));
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue()
    {
        foreach ($this->staticDefinitions as $staticDefinition) {
            if ($staticDefinition->staticTypeHasValue()) {
                // If the type of any definition in the model contains value information,
                // treat the whole model as having it so it can be displayed if needed
                return true;
            }
        }

        return false;
    }
}
