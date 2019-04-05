<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Router\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Router\Validation\Constraint\ValidParameterBagForUrlPatternConstraint;
use Combyna\Component\Router\Validation\Constraint\ValidParameterBagForUrlPatternConstraintValidator;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ValidParameterBagForUrlPatternConstraintValidatorTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidParameterBagForUrlPatternConstraintValidatorTest extends TestCase
{
    /**
     * @var ValidParameterBagForUrlPatternConstraint
     */
    private $constraint;

    /**
     * @var ObjectProphecy|FixedStaticBagModelNodeInterface
     */
    private $parameterBagModelNode;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    /**
     * @var ValidParameterBagForUrlPatternConstraintValidator
     */
    private $validator;

    public function setUp()
    {
        $this->parameterBagModelNode = $this->prophesize(FixedStaticBagModelNodeInterface::class);
        $this->constraint = new ValidParameterBagForUrlPatternConstraint(
            '/my/url/with/{param1}/and/{param2}',
            $this->parameterBagModelNode->reveal()
        );
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->parameterBagModelNode->getStaticDefinitionNames()->willReturn([
            'param1',
            'param2'
        ]);

        $this->validator = new ValidParameterBagForUrlPatternConstraintValidator();
    }

    public function testValidateAddsNoViolationsWhenThereAreNoPlaceholdersNorParameterDefinitions()
    {
        $this->parameterBagModelNode->getStaticDefinitionNames()->willReturn([]);
        $this->constraint = new ValidParameterBagForUrlPatternConstraint(
            '/my/url/with/no/placeholders',
            $this->parameterBagModelNode->reveal()
        );

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext->addGenericViolation(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function testValidateAddsViolationWhenSomePlaceholdersAreMissingParameterDefinitions()
    {
        $this->parameterBagModelNode->getStaticDefinitionNames()->willReturn([
            'param1'
            // param2 is not defined
        ]);

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Some URL parameter placeholder(s) are missing definitions: "param2"'
            )
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testValidateAddsViolationWhenSomeParameterDefinitionsAreForUndefinedPlaceholders()
    {
        $this->parameterBagModelNode->getStaticDefinitionNames()->willReturn([
            'param1',
            'ihavenoplaceholder' // Invalid as it is not part of the URL pattern
        ]);

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Unnecessary URL parameter definition(s): "ihavenoplaceholder"'
            )
            ->shouldHaveBeenCalledTimes(1);
    }
}
