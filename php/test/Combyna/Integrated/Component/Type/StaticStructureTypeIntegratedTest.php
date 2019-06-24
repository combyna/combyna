<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Component\Type;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\Expression\Evaluation\BagEvaluationContextFactoryInterface;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticStructureExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\NullValidationContext;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StaticStructureTypeIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureTypeIntegratedTest extends TestCase
{
    /**
     * @var BagEvaluationContextFactoryInterface
     */
    private $bagEvaluationContextFactory;

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var StaticStructureType
     */
    private $type;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->bagEvaluationContextFactory = $this->container->get('combyna.bag.evaluation_context_factory');
        $this->bagFactory = $this->container->get('combyna.bag.factory');
        $this->environmentFactory = $this->container->get('combyna.environment.factory');
        $this->evaluationContextFactory = $this->container->get('combyna.expression.evaluation_context_factory');
        $environment = $this->environmentFactory->create();
        $this->evaluationContext = $this->evaluationContextFactory->createRootContext($environment);
        $this->expressionFactory = $this->container->get('combyna.expression.factory');
        $this->staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationContext = new NullValidationContext();

        $this->type = new StaticStructureType(
            new FixedStaticBagModel(
                $this->bagFactory,
                $this->staticExpressionFactory,
                $this->bagEvaluationContextFactory,
                [
                    new FixedStaticDefinition(
                        'human',
                        new StaticStructureType(
                            new FixedStaticBagModel(
                                $this->bagFactory,
                                $this->staticExpressionFactory,
                                $this->bagEvaluationContextFactory,
                                [
                                    new FixedStaticDefinition(
                                        'first-name',
                                        new StaticType(TextExpression::class, $this->validationContext),
                                        new TextExpression('(default for first-name)')
                                    ),
                                    new FixedStaticDefinition(
                                        'second-name',
                                        new StaticType(TextExpression::class, $this->validationContext),
                                        new TextExpression('(default for second-name)')
                                    )
                                ]
                            ),
                            $this->validationContext
                        )
                    ),
                    new FixedStaticDefinition(
                        'dog',
                        new StaticStructureType(
                            new FixedStaticBagModel(
                                $this->bagFactory,
                                $this->staticExpressionFactory,
                                $this->bagEvaluationContextFactory,
                                [
                                    new FixedStaticDefinition(
                                        'name',
                                        new StaticType(TextExpression::class, $this->validationContext),
                                        new TextExpression('(default for name)')
                                    ),
                                    new FixedStaticDefinition(
                                        'food',
                                        new StaticType(TextExpression::class, $this->validationContext),
                                        new TextExpression('(default for food)')
                                    )
                                ]
                            ),
                            $this->validationContext
                        )
                    )
                ]
            ),
            $this->validationContext
        );
    }

    public function testAssociativeArrayWithOneCompleteAndOneIncompleteStructureSubElementIsCoercedCorrectly()
    {
        $coercedStatic = $this->type->coerceNative(
            [
                'human' => [
                    'first-name' => 'human name'
                    // NB: Human element is missing an expression for `second-name`,
                    //     so we should fall back to using the default expression for that structure attribute
                ],
                'dog' => [
                    'name' => 'dog name',
                    'food' => 'dog food'
                ]
            ],
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext
        );

        self::assertInstanceOf(StaticStructureExpression::class, $coercedStatic);
        self::assertEquals(
            [
                'human' => [
                    'first-name' => 'human name',
                    'second-name' => '(default for second-name)' // Ensure the coercion worked correctly
                ],
                'dog' => [
                    'name' => 'dog name',
                    'food' => 'dog food'
                ]
            ],
            $coercedStatic->toNative()
        );
    }

    public function testStructureStaticWithOneCompleteAndOneIncompleteStructureSubElementIsCoercedCorrectly()
    {
        $structureExpression = $this->expressionFactory->createStructureExpression(
            $this->bagFactory->createExpressionBag([
                'human' => $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([
                        'first-name' => $this->expressionFactory->createTextExpression('human name')
                        // NB: Human element is missing an expression for `second-name`,
                        //     so we should fall back to using the default expression for that structure attribute
                    ])
                ),
                'dog' => $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([
                        'name' => $this->expressionFactory->createTextExpression('dog name'),
                        'food' => $this->expressionFactory->createTextExpression('dog food')
                    ])
                )
            ])
        );
        $structureStatic = $structureExpression->toStatic($this->evaluationContext);

        $coercedStatic = $this->type->coerceStatic($structureStatic, $this->evaluationContext);

        self::assertInstanceOf(StaticStructureExpression::class, $coercedStatic);
        self::assertEquals(
            [
                'human' => [
                    'first-name' => 'human name',
                    'second-name' => '(default for second-name)' // Ensure the coercion worked correctly
                ],
                'dog' => [
                    'name' => 'dog name',
                    'food' => 'dog food'
                ]
            ],
            $coercedStatic->toNative()
        );
    }
}
