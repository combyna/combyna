<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Bag\Validation\Context\Specifier\ExpressionBagContextSpecifier;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use InvalidArgumentException;

/**
 * Class ExpressionBagNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagNode extends AbstractActNode
{
    const TYPE = 'expression-bag';

    /**
     * @var ExpressionNodeInterface[]
     */
    private $expressionNodes;

    /**
     * @param ExpressionNodeInterface[] $expressionNodes
     */
    public function __construct(array $expressionNodes)
    {
        $this->expressionNodes = $expressionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->defineValidationContext(new ExpressionBagContextSpecifier($this->expressionNodes));

        foreach ($this->expressionNodes as $expressionNode) {
            $specBuilder->addChildNode($expressionNode);
        }
    }

    /**
     * Fetches the specified expression from this bag
     *
     * @param string $name
     * @return ExpressionNodeInterface
     */
    public function getExpression($name)
    {
        if (!$this->hasExpression($name)) {
            throw new InvalidArgumentException(sprintf(
                'Expression bag contains no "%s" expression',
                $name
            ));
        }

        return $this->expressionNodes[$name];
    }

    /**
     * Fetches the names of all expressions in this bag
     *
     * @return string[]
     */
    public function getExpressionNames()
    {
        return array_keys($this->expressionNodes);
    }

    /**
     * Fetches all expressions in this bag
     *
     * @return ExpressionNodeInterface[]
     */
    public function getExpressions()
    {
        return $this->expressionNodes;
    }

    /**
     * Determines whether this bag contains an expression with the specified name
     *
     * @param string $name
     * @return bool
     */
    public function hasExpression($name)
    {
        return array_key_exists($name, $this->expressionNodes);
    }
}
