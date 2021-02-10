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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\VoidType;
use Combyna\Component\Validator\Type\AdditiveDeterminer;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ExpressionListNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionListNode extends AbstractActNode
{
    const TYPE = 'expression-list';

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
        foreach ($this->expressionNodes as $expressionNode) {
            $specBuilder->addChildNode($expressionNode);
        }
    }

    /**
     * Returns a type that represents all possible return types for the elements in the list
     * (eg. if all elements could only evaluate to NumberExpressions,
     *      then this would return StaticType<NumberExpression>. If one element
     *      could evaluate to a TextExpression, then it would return
     *      MultipleType<NumberExpression, TextExpression>)
     *
     * @return TypeDeterminerInterface
     */
    public function getElementResultTypeDeterminer()
    {
        if (count($this->expressionNodes) === 0) {
            // We cannot determine the type of the elements in an empty expression list,
            // as there are no elements to look at the types of
            return new PresolvedTypeDeterminer(new VoidType('expression list element type'));
        }

        /*
         * Otherwise, take all of the elements in the list and combine their types into one that would
         * accept any of their values, eg:
         * - if (the result types of all element expressions) are text, the element type would be `text`
         * - if (the result types of all element expressions) are numbers, the element type would be `number`
         * - if there are two elements, one whose expression result type is text and one whose is number,
         *   the element type would be `text|number`
         */

        return new AdditiveDeterminer(
            array_map(
                function (ExpressionNodeInterface $expressionNode) {
                    return $expressionNode->getResultTypeDeterminer();
                },
                $this->expressionNodes
            )
        );
    }

    /**
     * Fetches all expressions in this list
     *
     * @return ExpressionNodeInterface[]
     */
    public function getExpressions()
    {
        return $this->expressionNodes;
    }
}
