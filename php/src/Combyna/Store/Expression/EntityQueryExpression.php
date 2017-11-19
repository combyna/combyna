<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class EntityQueryExpression
 *
 * Makes a query against the entity by calling one of its defined query methods
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityQueryExpression extends AbstractExpression
{
    /**
     * @var ExpressionBagInterface
     */
    private $argumentExpressions;

    /**
     * @var SingleEntitySearchInterface
     */
    private $entitySearch;

    /**
     * @var EntityExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param EntityExpressionFactoryInterface $expressionFactory
     * @param SingleEntitySearchInterface $entitySearch
     * @param string $queryName
     * @param ExpressionBagInterface $argumentExpressions
     */
    public function __construct(
        EntityExpressionFactoryInterface $expressionFactory,
        SingleEntitySearchInterface $entitySearch,
        $queryName,
        ExpressionBagInterface $argumentExpressions
    ) {
        $this->argumentExpressions = $argumentExpressions;
        $this->entitySearch = $entitySearch;
        $this->expressionFactory = $expressionFactory;
        $this->queryName = $queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'entity-query';
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        try {
            $entity = $this->entitySearch->getEntity();
        } catch (EntityNotFoundException $exception) {
            // TODO: Consider:
            // - a) A special "TryCatchExpression" that must be used around any expression that may result in an ErrorExpression (other validation will fail)
            // - b) Enforcing (via validation) an ancestor ConditionalExpression that ensures eg. the entity will always be found
            // - c) This exp always returns a StaticListExpr with query result from calling against every matched entity (match multiple)
            //      so failure (no entity found) would be represented with just an empty result list
            // - d) A special IfEntityExists expression so that we can statically validate that existence is checked for first
            //
            // Things to consider:
            // - Should EntitySearches be defined on the store to prevent repeating ourselves?
            //   This expr could then just take searchName as an arg
            // - What do we do if multiple entities are matched at run time?

            return $this->expressionFactory->createErrorExpression(
                $exception,
                $evaluationContext->getStackSnapshot()
            );
        }

        if (!$this->entity->definesQuery($this->queryName)) {
            return $this->expressionFactory->createErrorExpression(
                new EntityQueryNotFoundException($entity, $this->queryName),
                $evaluationContext->getStackSnapshot()
            );
        }

        $argumentStaticBag = $this->argumentExpressions->toStaticBag($evaluationContext);

        return $entity->makeQuery($this->queryName, $argumentStaticBag, $evaluationContext);
    }
}
