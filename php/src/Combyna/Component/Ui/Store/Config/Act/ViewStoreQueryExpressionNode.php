<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Store\Expression\ViewStoreQueryExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewStoreQueryExpressionNode
 *
 * Makes a query defined on a view store
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryExpressionNode extends AbstractExpressionNode
{
    const TYPE = ViewStoreQueryExpression::TYPE;

    /**
     * @var ExpressionBagNode|null
     */
    private $argumentExpressionBag;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param string $queryName
     * @param ExpressionBagNode|null $argumentExpressionBag
     */
    public function __construct($queryName, ExpressionBagNode $argumentExpressionBag = null)
    {
        $this->argumentExpressionBag = $argumentExpressionBag;
        $this->queryName = $queryName;
    }

    /**
     * Fetches the bag of expressions for any parameters of the message, if set
     *
     * @return ExpressionBagNode|null
     */
    public function getArgumentExpressionBag()
    {
        return $this->argumentExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
//        return $validationContext->getViewStoreQueryType($this->queryName);

        // FIXME!
        return new StaticType(TextExpression::class);
    }

    /**
     * Fetches the name of the query to be made
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

//        $subValidationContext->assertInsideView();
//        $subValidationContext->assertViewHasQuery($this->queryName);

        if ($this->argumentExpressionBag !== null) {
            $this->argumentExpressionBag->validate($subValidationContext);
        }

//        $subValidationContext->assertValidViewQuery($this->argumentExpressionBag);
    }
}
