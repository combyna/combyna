<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\AssuredExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AssuredExpressionNode
 *
 * Returns an "assured" static, evaluated by an ancestor expression
 * and guaranteed to satisfy a condition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpressionNode extends AbstractExpressionNode
{
    const TYPE = AssuredExpression::TYPE;

    /**
     * @var string
     */
    private $assuredStaticName;

    /**
     * @param string $assuredStaticName
     */
    public function __construct(
        $assuredStaticName
    ) {
        $this->assuredStaticName = $assuredStaticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return $validationContext->getAssuredStaticType($this->assuredStaticName);
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

    /**
     * Promotes this node to an actual AssuredExpression
     *
     * @param ExpressionFactoryInterface $expressionFactory
     * @return AssuredExpression
     */
    public function promote(ExpressionFactoryInterface $expressionFactory)
    {
        return $expressionFactory->createAssuredExpression($this->assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $subValidationContext->assertAssuredStaticExists($this->assuredStaticName);
    }
}
