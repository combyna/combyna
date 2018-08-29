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
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Class ExpressionBag
 *
 * Represents a bag of related name/value pairs, where the values must be ExpressionInterface objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBag implements ExpressionBagInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param ExpressionInterface[] $expressions
     */
    public function __construct(BagFactoryInterface $bagFactory, array $expressions)
    {
        $this->assertValidExpressions($expressions);

        $this->bagFactory = $bagFactory;
        $this->expressions = $expressions;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression($name)
    {
        if (!$this->hasExpression($name)) {
            throw new InvalidArgumentException(sprintf(
                'Expression bag contains no "%s" expression',
                $name
            ));
        }

        return $this->expressions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionNames()
    {
        return array_keys($this->expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function hasExpression($name)
    {
        return array_key_exists($name, $this->expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function toStaticBag(EvaluationContextInterface $evaluationContext)
    {
        /** @var StaticInterface[] $statics */
        $statics = [];

        foreach ($this->expressions as $name => $expression) {
            $statics[$name] = $expression->toStatic($evaluationContext);
        }

        return $this->bagFactory->createStaticBag($statics);
    }

    /**
     * Validates that all expressions in the provided bag are actually ExpressionInterfaces
     *
     * @param ExpressionInterface[] $expressions
     */
    private function assertValidExpressions(array $expressions)
    {
        foreach ($expressions as $name => $expression) {
            if (!$expression instanceof ExpressionInterface) {
                throw new InvalidArgumentException(
                    'Bag expression "' . $name . '" is actually a ' . get_class($expression)
                );
            }
        }
    }
}
