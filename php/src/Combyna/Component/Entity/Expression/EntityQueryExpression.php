<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Entity\Evaluation\EntityEvaluationContextInterface;
use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Exception\EntityQueryNotFoundException;
use InvalidArgumentException;

/**
 * Class EntityQueryExpression
 *
 * Makes a query against the current entity by calling one of its defined query methods
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
     * @var EntityExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param EntityExpressionFactoryInterface $expressionFactory
     * @param string $queryName
     * @param ExpressionBagInterface $argumentExpressions
     */
    public function __construct(
        EntityExpressionFactoryInterface $expressionFactory,
        $queryName,
        ExpressionBagInterface $argumentExpressions
    ) {
        $this->argumentExpressions = $argumentExpressions;
        $this->expressionFactory = $expressionFactory;
        $this->queryName = $queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'entity-self-query';
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityEvaluationContextInterface $evaluationContext
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        if (!$evaluationContext instanceof EntityEvaluationContextInterface) {
            throw new InvalidArgumentException(
                'Expected an EntityEvaluationContextInterface, got "' . get_class($evaluationContext) . '"'
            );
        }

        $entity = $evaluationContext->getEntity();

        if (!$entity->definesQuery($this->queryName)) {
            // FIXME: Needs to be prevented by validation
            throw new EntityQueryNotFoundException($entity, $this->queryName);
        }

        $argumentStaticBag = $this->argumentExpressions->toStaticBag($evaluationContext);

        return $entity->makeQuery($this->queryName, $argumentStaticBag, $evaluationContext);
    }
}
