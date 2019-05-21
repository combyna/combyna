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

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticDefinitionInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use LogicException;

/**
 * Class FixedStaticDefinition
 *
 * Defines the name, type and default static value for a static in a bag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticDefinition implements FixedStaticDefinitionInterface
{
    /**
     * @var ExpressionInterface|null
     */
    private $defaultExpression;

    /**
     * @var string
     */
    private $name;

    /**
     * @var TypeInterface
     */
    private $staticType;

    /**
     * @param string $name
     * @param TypeInterface $staticType
     * @param ExpressionInterface|null $defaultExpression
     */
    public function __construct(
        $name,
        TypeInterface $staticType,
        ExpressionInterface $defaultExpression = null
    ) {
        $this->defaultExpression = $defaultExpression;
        $this->name = $name;
        $this->staticType = $staticType;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStatic(StaticInterface $static)
    {
        return $this->staticType->allowsStatic($static);
    }

    /**
     * {@inheritdoc}
     */
    public function allowsStaticDefinition(DeterminedFixedStaticDefinitionInterface $otherDefinition)
    {
        return $this->staticType->allows($otherDefinition->getStaticType());
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(EvaluationContextInterface $evaluationContext, StaticInterface $static = null)
    {
        if ($static === null) {
            // No value was provided for the defined static - use the default value for the static if defined
            // (if not defined, an exception will be thrown, as validation should have ensured
            // that a static that is able to not be set always has a default expression defined)
            $static = $this->getDefaultStatic($evaluationContext);
        }

        return $this->staticType->coerceStatic($static, $evaluationContext);
    }

    /**
     * Fetches the default value for this static, if configured
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     * @throws LogicException when no default static has been configured
     */
    public function getDefaultStatic(EvaluationContextInterface $evaluationContext)
    {
        if (!$this->defaultExpression) {
            throw new LogicException(
                'No default expression has been configured for parameter "' . $this->name . '"'
            );
        }

        return $this->defaultExpression->toStatic($evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType()
    {
        return $this->staticType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTypeSummary()
    {
        return $this->staticType->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->defaultExpression === null;
    }
}
