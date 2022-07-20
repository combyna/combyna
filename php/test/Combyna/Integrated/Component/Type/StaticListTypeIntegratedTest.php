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
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\NullValidationContext;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StaticListTypeIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTypeIntegratedTest extends TestCase
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
        $this->expressionFactory = $this->container->get('combyna.expression.factory');
        $this->staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationContext = new NullValidationContext();
    }

    public function testListWithOneCompleteAndOneIncompleteStructureElementIsCoercedCorrectly()
    {
        $listType = new StaticListType(
            new StaticStructureType(
                new FixedStaticBagModel(
                    $this->bagFactory,
                    $this->staticExpressionFactory,
                    $this->bagEvaluationContextFactory,
                    [
                        new FixedStaticDefinition(
                            'first-attr',
                            new StaticType(TextExpression::class, $this->validationContext),
                            new TextExpression('default for first-attr')
                        ),
                        new FixedStaticDefinition(
                            'second-attr',
                            new StaticType(TextExpression::class, $this->validationContext),
                            new TextExpression('default for second-attr')
                        )
                    ]
                ),
                $this->validationContext
            ),
            $this->validationContext
        );
        $listExpression = $this->expressionFactory->createListExpression(
            $this->bagFactory->createExpressionList([
                $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([
                        'first-attr' => $this->expressionFactory->createTextExpression('element 1, first-attr')
                        // NB: First element is missing an expression for `second-attr`,
                        //     so we should fall back to using the default expression for that structure attribute
                    ])
                ),
                $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([
                        'first-attr' => $this->expressionFactory->createTextExpression('element 2, first-attr'),
                        'second-attr' => $this->expressionFactory->createTextExpression('element 2, second-attr')
                    ])
                )
            ])
        );
        $environment = $this->environmentFactory->create();
        $evaluationContext = $this->evaluationContextFactory->createRootContext($environment);
        $listStatic = $listExpression->toStatic($evaluationContext);

        $coercedStatic = $listType->coerceStatic($listStatic, $evaluationContext);

        static::assertInstanceOf(StaticListExpression::class, $coercedStatic);
        static::assertCount(2, $coercedStatic->getElementStatics());
        static::assertSame(
            'element 1, first-attr',
            $coercedStatic->getElementStatics()[0]
                ->getAttributeStatic('first-attr')
                ->toNative()
        );
        static::assertSame(
            // NB: The default expression should have been evaluated and used,
            //     because the first element didn't specify a value for second-attr (see above)
            'default for second-attr',
            $coercedStatic->getElementStatics()[0]
                ->getAttributeStatic('second-attr')
                ->toNative()
        );
        static::assertSame(
            'element 2, first-attr',
            $coercedStatic->getElementStatics()[1]
                ->getAttributeStatic('first-attr')
                ->toNative()
        );
        static::assertSame(
            'element 2, second-attr',
            $coercedStatic->getElementStatics()[1]
                ->getAttributeStatic('second-attr')
                ->toNative()
        );
    }
}
