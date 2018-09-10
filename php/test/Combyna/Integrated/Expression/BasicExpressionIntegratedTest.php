<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression;

use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Environment;
use Combyna\Component\Environment\Library\FunctionCollection;
use Combyna\Component\Environment\Library\Library;
use Combyna\Component\Environment\Library\NativeFunction;
use Combyna\Component\Event\EventDefinitionCollection;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactory;
use Combyna\Component\Expression\ExpressionFactory;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Signal\SignalDefinitionCollection;
use Combyna\Component\Type\StaticType;
use Combyna\Harness\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

/**
 * Class BasicExpressionIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BasicExpressionIntegratedTest extends TestCase
{
    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var EvaluationContextFactory
     */
    private $evaluationContextFactory;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    public function setUp()
    {
        $staticExpressionFactory = new StaticExpressionFactory();
        $translator = new Translator('en');
        $translator->addLoader('yaml', new ArrayLoader());
        $this->bagFactory = new BagFactory($staticExpressionFactory);
        $this->environment = new Environment($translator);
        $this->evaluationContextFactory = new EvaluationContextFactory($this->bagFactory);

        $this->environment->installLibrary(
            new Library(
                'text',
                new FunctionCollection([
                    new NativeFunction(
                        'length',
                        new FixedStaticBagModel(
                            $this->bagFactory,
                            [
                                new FixedStaticDefinition('textString')
                            ]
                        ),
                        function (StaticBagInterface $argumentBag) {
                            $textString = $argumentBag->getStatic('textString')->toNative();

                            return $this->expressionFactory->createNumberExpression(strlen($textString));
                        }
                    )
                ], 'text'),
                new EventDefinitionCollection([], 'text'),
                new SignalDefinitionCollection([], 'text'),
                [],
                [
                    'en' => [
                        'my' => [
                            'translation' => [
                                'key' => ' and done'
                            ]
                        ]
                    ]
                ]
            )
        );

        $this->expressionFactory = new ExpressionFactory(
            $staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContextFactory
        );
    }

    public function testAllBasicExpressionsTogether()
    {
        $expression = $this->expressionFactory->createConditionalExpression(
            $this->expressionFactory->createComparisonExpression(
                $this->expressionFactory->createComparisonExpression(
                    $this->expressionFactory->createFunctionExpression(
                        'text',
                        'length',
                        $this->bagFactory->createExpressionBag([
                            'textString' => $this->expressionFactory->createTextExpression('Fred')
                        ]),
                        new StaticType(NumberExpression::class)
                    ),
                    ComparisonExpression::EQUAL,
                    $this->expressionFactory->createNumberExpression(21)
                ),
                ComparisonExpression::EQUAL,
                $this->expressionFactory->createBooleanExpression(true)
            ),
            $this->expressionFactory->createTextExpression('wrong'),
            $this->expressionFactory->createConcatenationExpression(
                $this->expressionFactory->createListExpression(
                    $this->bagFactory->createExpressionList([
                        $this->expressionFactory->createBinaryArithmeticExpression(
                            $this->expressionFactory->createConversionExpression(
                                $this->expressionFactory->createTextExpression('7'),
                                ConversionExpression::TEXT_TO_NUMBER
                            ),
                            BinaryArithmeticExpression::MULTIPLY,
                            $this->expressionFactory->createNumberExpression(2)
                        ),
                        $this->expressionFactory->createTextExpression(' is my result'),
                        $this->expressionFactory->createGuardExpression(
                            [
                                $this->expressionFactory->createGuardAssurance(
                                    $this->expressionFactory->createConversionExpression(
                                        $this->expressionFactory->createTextExpression('0'),
                                        ConversionExpression::TEXT_TO_NUMBER
                                    ),
                                    AssuranceInterface::NON_ZERO_NUMBER,
                                    'myNonZeroValue'
                                )
                            ],
                            $this->expressionFactory->createBinaryArithmeticExpression(
                                // Will never be evaluated, as the assured value does not match the constraint
                                $this->expressionFactory->createNumberExpression(2),
                                BinaryArithmeticExpression::DIVIDE,
                                $this->expressionFactory->createAssuredExpression('myNonZeroValue')
                            ),
                            $this->expressionFactory->createTextExpression(' - it was zero, oops')
                        ),
                        $this->expressionFactory->createConditionalExpression(
                            $this->expressionFactory->createComparisonExpression(
                                $this->expressionFactory->createTextExpression('hello'),
                                ComparisonExpression::EQUAL_CASE_INSENSITIVE,
                                $this->expressionFactory->createTextExpression('heLLO')
                            ),
                            $this->expressionFactory->createTextExpression(' - insensitively equal '),
                            $this->expressionFactory->createTextExpression('wrong')
                        ),
                        $this->expressionFactory->createConcatenationExpression(
                            $this->expressionFactory->createMapExpression(
                                $this->expressionFactory->createListExpression(
                                    $this->bagFactory->createExpressionList([
                                        $this->expressionFactory->createNumberExpression(10),
                                        $this->expressionFactory->createNumberExpression(50)
                                    ])
                                ),
                                'my_item',
                                'my_item_index',
                                $this->expressionFactory->createConcatenationExpression(
                                    $this->expressionFactory->createListExpression(
                                        $this->bagFactory->createExpressionList([
                                            $this->expressionFactory->createVariableExpression('my_item_index'),
                                            $this->expressionFactory->createTextExpression('='),
                                            $this->expressionFactory->createBinaryArithmeticExpression(
                                                $this->expressionFactory->createVariableExpression('my_item'),
                                                BinaryArithmeticExpression::MULTIPLY,
                                                $this->expressionFactory->createNumberExpression(2)
                                            )
                                        ])
                                    )
                                )
                            ),
                            $this->expressionFactory->createTextExpression(':')
                        ),
                        $this->expressionFactory->createTranslationExpression('my.translation.key')
                    ])
                )
            )
        );
        $evaluationContext = $this->evaluationContextFactory->createRootContext($this->environment);

        $resultStatic = $expression->toStatic($evaluationContext);

        $this->assertInstanceOf(TextExpression::class, $resultStatic);
        $this->assert($resultStatic->toNative())->exactlyEquals(
            '14 is my result - it was zero, oops - insensitively equal 1=20:2=100 and done'
        );
    }
}
