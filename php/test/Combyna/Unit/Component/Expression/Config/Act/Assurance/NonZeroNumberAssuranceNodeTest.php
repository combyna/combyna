<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Config\Act\Assurance\NonZeroNumberAssuranceNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NonZeroNumberAssuranceNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $inputExpressionNode;

    /**
     * @var NonZeroNumberAssuranceNode
     */
    private $node;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->inputExpressionNode = $this->prophesize(ExpressionNodeInterface::class);

        $this->node = new NonZeroNumberAssuranceNode($this->inputExpressionNode->reveal(), 'my-static');
    }

    public function testGetConstraintReturnsCorrectValue()
    {
        $this->assert($this->node->getConstraint())->exactlyEquals(NonZeroNumberAssurance::TYPE);
    }

    public function testGetRequiredAssuredStaticNamesReturnsAnArrayWithJustTheStaticName()
    {
        $this->assert($this->node->getAssuredStaticName())->exactlyEquals('my-static');
    }
}
