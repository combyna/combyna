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

/**
 * Class VariableExpression
 *
 * Returns the static value of a variable in the current context
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExpression extends AbstractExpression
{
    const TYPE = 'variable';

    /**
     * @var string
     */
    private $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $evaluationContext->getVariable($this->variableName);
    }
}
