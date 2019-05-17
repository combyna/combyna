<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class QueriedResultTypeDeterminer
 *
 * Defines a type that is dynamically fetched by a query.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueriedResultTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'queried';

    /**
     * @var ActNodeInterface
     */
    private $node;

    /**
     * @var ResultTypeQueryInterface
     */
    private $resultTypeQuery;

    /**
     * @param ResultTypeQueryInterface $resultTypeQuery
     * @param ActNodeInterface $node
     */
    public function __construct(
        ResultTypeQueryInterface $resultTypeQuery,
        ActNodeInterface $node
    ) {
        $this->node = $node;
        $this->resultTypeQuery = $resultTypeQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return $validationContext->queryForResultType(
            $this->resultTypeQuery,
            $this->node
        );
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->resultTypeQuery->makesQuery($querySpecifier);
    }
}
