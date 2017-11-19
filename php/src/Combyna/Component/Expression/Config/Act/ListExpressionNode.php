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
use Combyna\Component\Expression\ListExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticListType;

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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        return new StaticListType($this->expressionList->getElementResultType($subValidationContext));
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->expressionList->validate($subValidationContext);
    }
}
