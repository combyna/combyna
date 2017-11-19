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
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AssuredExpression
 *
 * Returns an "assured" static, evaluated by an ancestor expression
 * and guaranteed to satisfy a condition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpression extends AbstractExpression
{
    const TYPE = 'assured';

    /**
     * @var string
     */
    private $assuredStaticName;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param string $assuredStaticName
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        $assuredStaticName
    ) {
        $this->assuredStaticName = $assuredStaticName;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $evaluationContext->getAssuredStatic($this->assuredStaticName);
    }

    /**
     * Fetches the assurance for the assured static
     *
     * @param ValidationContextInterface $validationContext
     * @return AssuranceInterface
     */
    public function getAssurance(ValidationContextInterface $validationContext)
    {
        return $validationContext->getAssuredStaticAssurance($this->assuredStaticName);
    }

    /**
     * Fetches the name of the assured static
     *
     * @return string
     */
    public function getAssuredStaticName()
    {
        return $this->assuredStaticName;
    }
}
