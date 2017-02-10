<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\StaticType;

/**
 * Class AbstractStaticExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractStaticExpression implements StaticInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        // Nothing to validate, a static expression should have no operands as it is already evaluated
    }
}
