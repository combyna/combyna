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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;

/**
 * Class AttributeExpression
 *
 * Fetches an attribute of a structure
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AttributeExpression extends AbstractExpression
{
    const TYPE = 'attribute';

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var ExpressionInterface
     */
    private $structureExpression;

    /**
     * @param ExpressionInterface $structureExpression
     * @param string $attributeName
     */
    public function __construct(
        ExpressionInterface $structureExpression,
        $attributeName
    ) {
        $this->attributeName = $attributeName;
        $this->structureExpression = $structureExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $staticStructure = $this->structureExpression->toStatic($subEvaluationContext);

        if (!$staticStructure instanceof StaticStructureExpression) {
            throw new InvalidArgumentException(sprintf(
                'Expected expression to resolve to a StaticStructureExpression, got "%s"',
                get_class($staticStructure)
            ));
        }

        return $staticStructure->getAttributeStatic($this->attributeName);
    }
}
