<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Validation\Constraint;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\StructureHasAttributeConstraint;
use Combyna\Component\Expression\Validation\Constraint\StructureHasAttributeConstraintValidator;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StructureHasAttributeConstraintValidatorTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureHasAttributeConstraintValidatorTest extends TestCase
{
    /**
     * @var StructureHasAttributeConstraint
     */
    private $constraint;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $structureExpressionNode;

    /**
     * @var ObjectProphecy|StaticStructureType
     */
    private $structureResultType;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    /**
     * @var StructureHasAttributeConstraintValidator
     */
    private $validator;

    public function setUp()
    {
        $this->structureExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->structureResultType = $this->prophesize(StaticStructureType::class);
        $this->constraint = new StructureHasAttributeConstraint(
            $this->structureExpressionNode->reveal(),
            'my-attr'
        );
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->structureResultType->hasAttribute('my-attr')
            ->willReturn(true);
        $this->validationContext
            ->addGenericViolation(Argument::cetera())
            ->willReturn(null);
        $this->validationContext->getExpressionResultType($this->structureExpressionNode)
            ->willReturn($this->structureResultType);

        $this->validator = new StructureHasAttributeConstraintValidator();
    }

    public function testValidateAddsNoViolationsWhenTheStructureContainsTheAttribute()
    {
        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext->addGenericViolation(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function testValidateAddsViolationWhenExpressionResolvesToAStructureThatDoesntDefineTheAttribute()
    {
        $this->structureResultType->hasAttribute('my-attr')
            ->willReturn(false);

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Structure does not define an attribute with name "my-attr"'
            )
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testValidateAddsViolationWhenExpressionDoesNotResolveToAStructure()
    {
        $nonStructureType = $this->prophesize(StaticType::class);
        $nonStructureType->getSummary()
            ->willReturn('not-a-structure');
        $this->validationContext->getExpressionResultType($this->structureExpressionNode)
            ->willReturn($nonStructureType);

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Structure expression should result in a structure, but results in a "not-a-structure" instead'
            )
            ->shouldHaveBeenCalledTimes(1);
    }
}
