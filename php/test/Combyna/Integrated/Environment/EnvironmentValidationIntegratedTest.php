<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Environment;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Config\Act\HomeNode;
use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Environment\Config\Act\NativeFunctionNode;
use Combyna\Component\Event\Config\Act\EventDefinitionNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;
use Combyna\Component\Instruction\Config\Act\UnknownInstructionNode;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Ui\Config\Act\ChildWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\PrimitiveWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\TextWidgetNode;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use Combyna\Component\Validator\ValidationFactory;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EnvironmentValidationIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentValidationIntegratedTest extends TestCase
{
    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var NodeValidator
     */
    private $nodeValidator;

    /**
     * @var ValidationFactory
     */
    private $validationFactory;

    public function setUp()
    {
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $this->container = $combynaBootstrap->createContainer();

        $staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationFactory = $this->container->get('combyna.validator.factory');
        $this->bagFactory = new BagFactory($staticExpressionFactory);
        $this->nodeValidator = $this->container->get('combyna.program.node_validator');
    }

    public function testAllNonExpressionNodeTypesAreValidated()
    {
        $environmentNode = new EnvironmentNode([
            new LibraryNode(
                'my_lib',
                'My fancy library',
                [],
                [
                    new NativeFunctionNode(
                        'my_lib',
                        'my_func',
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-param',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid func param type'))
                            )
                        ]),
                        new PresolvedTypeDeterminer(new UnresolvedType('invalid func return type'))
                    )
                ],
                [
                    new EventDefinitionNode(
                        'my_event',
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-payload-static',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid event payload static type'))
                            )
                        ])
                    )
                ],
                [
                    new SignalDefinitionNode(
                        'my_signal',
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-payload-static',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid signal payload static type'))
                            )
                        ])
                    )
                ],
                [
                    new PrimitiveWidgetDefinitionNode(
                        'my_lib',
                        'my_widget',
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-widget-attr',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid widget attr type'))
                            )
                        ]),
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-widget-value',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid widget value type'))
                            )
                        ]),
                        [
                            new ChildWidgetDefinitionNode('a_child')
                        ],
                        [
                            new EventDefinitionReferenceNode(
                                'some_undefined_lib',
                                'some_event'
                            )
                        ]
                    ),
                    new CompoundWidgetDefinitionNode(
                        'my_lib',
                        'my_widget',
                        new FixedStaticBagModelNode([
                            new FixedStaticDefinitionNode(
                                'my-invalid-widget-attr',
                                new PresolvedTypeDeterminer(new UnresolvedType('invalid widget attr type'))
                            )
                        ]),
                        new ExpressionBagNode([
                            'my-invalid-widget-value' => new UnknownExpressionTypeNode('unknown widget value expression type')
                        ]),
                        [
                            new ChildWidgetDefinitionNode('a_child')
                        ],
                        [
                            new EventDefinitionReferenceNode(
                                'some_undefined_lib',
                                'some_event'
                            )
                        ],
                        new DefinedWidgetNode(
                            'some_undefined_lib',
                            'some_widget',
                            new ExpressionBagNode([
                                'some_attr' => new UnknownExpressionTypeNode('unknown_expr_type')
                            ]),
                            new FixedStaticBagModelNode([]),
                            new ExpressionBagNode([]),
                            'root',
                            [
                                'some_child' => new TextWidgetNode(
                                    new UnknownExpressionTypeNode('unknown_type'),
                                    new FixedStaticBagModelNode([]),
                                    new ExpressionBagNode([])
                                )
                            ],
                            [
                                new TriggerNode(
                                    new EventDefinitionReferenceNode(
                                        'some_undefined_lib',
                                        'some_event'
                                    ),
                                    [
                                        new UnknownInstructionNode('some_unknown_instruction')
                                    ]
                                )
                            ]
                        )
                    )
                ]
            )
        ]);
        $appNode = new AppNode(
            $environmentNode,
            [],
            [],
            new HomeNode('app', 'home', new ExpressionBagNode([])),
            [],
            []
        );
        $environmentNode->installNativeFunction('my_lib', 'my_func', function () {});

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [environment].[library:my_lib].[event-definition:my_event].[fixed-static-bag-model].[fixed-static-definition:my-invalid-payload-static]' .
            ' - Expected type not to be unresolved, but it was: unknown<invalid event payload static type>. :: ' .

            'ACT node [environment].[library:my_lib].[native-function:my_func]' .
            ' - Expected type not to be unresolved, but it was: unknown<invalid func return type>. :: ' .

            'ACT node [environment].[library:my_lib].[native-function:my_func].[fixed-static-bag-model].[fixed-static-definition:my-invalid-param]' .
            ' - Expected type not to be unresolved, but it was: unknown<invalid func param type>. :: ' .

            'ACT node [environment].[library:my_lib].[signal-definition:my_signal].[fixed-static-bag-model].[fixed-static-definition:my-invalid-payload-static]' .
            ' - Expected type not to be unresolved, but it was: unknown<invalid signal payload static type>. :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[fixed-static-bag-model].[fixed-static-definition:my-invalid-widget-attr]' .
            ' - Expected type not to be unresolved, but it was: unknown<invalid widget attr type>. :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[event-definition-reference]' .
            ' - Event "some_undefined_lib.some_event" is not defined. :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[unknown-library-for-widget-definition]' .
            ' - Library "some_undefined_lib" does not exist in order to define widget definition "some_widget". :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[expression-bag].[unknown]' .
            ' - Expression is of unknown type "unknown_expr_type". :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[act-node].[event-definition-reference]' .
            ' - Event "some_undefined_lib.some_event" is not defined. :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[act-node].[trigger:some_undefined_lib.some_event].[act-node]' .
            ' - Instruction is of unknown type "some_unknown_instruction". :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[text-widget]' .
            ' - text would get [unknown<Expression type "unknown_type">], expects [text]. :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[defined-widget:root].[text-widget].[unknown]' .
            ' - Expression is of unknown type "unknown_type". :: ' .

            'ACT node [environment].[library:my_lib].[compound-widget-def:my_widget].[expression-bag].[unknown]' .
            ' - Expression is of unknown type "unknown widget value expression type"'
        );

        $this->nodeValidator->validate($environmentNode, $appNode)->throwIfViolated();
    }
}
