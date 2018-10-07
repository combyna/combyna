<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Ui\Validation\Constraint;

use Combyna\Component\Ui\Validation\Constraint\ValidWidgetValueProvidersConstraint;
use Combyna\Component\Ui\Validation\Constraint\ValidWidgetValueProvidersConstraintValidator;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ValidWidgetValueProvidersConstraintValidatorTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidWidgetValueProvidersConstraintValidatorTest extends TestCase
{
    /**
     * @var ValidWidgetValueProvidersConstraint
     */
    private $constraint;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    /**
     * @var ValidWidgetValueProvidersConstraintValidator
     */
    private $validator;

    public function setUp()
    {
        $this->constraint = new ValidWidgetValueProvidersConstraint(
            'my_lib',
            'my_widget',
            ['first_value', 'second_value'],
            function () {
                return [
                    'first_value' => function () {},
                    'second_value' => function () {}
                ];
            }
        );
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->validator = new ValidWidgetValueProvidersConstraintValidator();
    }

    public function testValidateAddsNoViolationsWhenThereAreNoValuesNorProviders()
    {
        $this->constraint = new ValidWidgetValueProvidersConstraint(
            'my_lib',
            'my_widget',
            [],
            function () {
                return [];
            }
        );

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext->addGenericViolation(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function testValidateAddsViolationWhenSomeValuesAreMissingProviders()
    {
        $this->constraint = new ValidWidgetValueProvidersConstraint(
            'my_lib',
            'my_widget',
            ['first_val', 'second_val'],
            function () {
                return [];
            }
        );

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Some value(s) are missing providers: "first_val", "second_val"'
            )
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testValidateAddsViolationWhenSomeProvidersAreForUndefinedValues()
    {
        $this->constraint = new ValidWidgetValueProvidersConstraint(
            'my_lib',
            'my_widget',
            [],
            function () {
                return ['undefined_value' => function () {}];
            }
        );

        $this->validator->validate($this->constraint, $this->validationContext->reveal());

        $this->validationContext
            ->addGenericViolation(
                'Unnecessary value provider(s): "undefined_value"'
            )
            ->shouldHaveBeenCalledTimes(1);
    }
}
