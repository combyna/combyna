<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Ui\Store\Expression\ViewStoreQueryExpression;
use Combyna\Component\Ui\Store\Validation\Constraint\ValidViewStoreQueryConstraint;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreQueryResultTypeQuery;
use Combyna\Component\Ui\Validation\Constraint\InsideViewConstraint;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        if ($this->argumentExpressionBag !== null) {
            $specBuilder->addChildNode($this->argumentExpressionBag);
        }

        $specBuilder->addConstraint(new InsideViewConstraint());
        $specBuilder->addConstraint(
            new ValidViewStoreQueryConstraint(
                $this->queryName,
                $this->argumentExpressionBag
            )
        );
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
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new ViewStoreQueryResultTypeQuery($this->queryName),
            $this
        );
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
}
