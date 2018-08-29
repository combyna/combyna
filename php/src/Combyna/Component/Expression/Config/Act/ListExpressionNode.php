<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionListNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\ListExpression;
use Combyna\Component\Validator\Type\StaticListTypeDeterminer;

/**
 * Class ListExpressionNode
 *
 * Contains a list of expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListExpressionNode extends AbstractExpressionNode
{
    const TYPE = ListExpression::TYPE;

    /**
     * @var ExpressionListNode
     */
    private $expressionList;

    /**
     * @param ExpressionListNode $expressionList
     */
    public function __construct(ExpressionListNode $expressionList)
    {
        $this->expressionList = $expressionList;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->expressionList);
    }

    /**
     * Fetches the list of expressions that will form the elements of the list
     *
     * @return ExpressionListNode
     */
    public function getExpressionList()
    {
        return $this->expressionList;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StaticListTypeDeterminer($this->expressionList->getElementResultTypeDeterminer());
    }
}
