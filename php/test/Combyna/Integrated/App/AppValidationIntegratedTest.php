<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\App;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Config\Act\HomeNode;
use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNode;
use Combyna\Component\Config\Act\UnknownNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Ui\Config\Act\TextWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\ValidationFactory;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AppValidationIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppValidationIntegratedTest extends TestCase
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
     * @var EnvironmentNode
     */
    private $environmentNode;

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
        $this->container = $combynaBootstrap->getContainer();

        $staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationFactory = $this->container->get('combyna.validator.factory');
        $this->bagFactory = new BagFactory($staticExpressionFactory);
        $this->environmentNode = new EnvironmentNode([
            new LibraryNode(
                'text',
                'A library for processing of text data'
            )
        ]);
        $this->nodeValidator = $this->container->get('combyna.program.node_validator');
    }

    public function testAllNonExpressionNodeTypesAreValidated()
    {
        $appNode = new AppNode(
            $this->environmentNode,
            [],
            [],
            new HomeNode('app', 'home', new ExpressionBagNode([])),
            [
                new PageViewNode(
                    'my-very-invalid-app',
                    new NumberExpressionNode(21), // Wrong type, title should evaluate to text
                    'A very invalid app by me',
                    new FixedStaticBagModelNode([
                        new FixedStaticDefinitionNode(
                            'invalid-attr',
                            new StaticType(TextExpression::class),
                            new UnknownExpressionNode('invalid-type-for-page-attr')
                        )
                    ]),
                    new WidgetGroupNode(
                        [
                            new DefinedWidgetNode(
                                'invalid-lib',
                                'invalid-widget',
                                new ExpressionBagNode([
                                    'some-attr' => new UnknownExpressionNode('invalid-type-for-widget-attr')
                                ]),
                                0,
                                [
                                    new TextWidgetNode(
                                        new NumberExpressionNode(101), // Wrong type, should evaluate to text
                                        new NumberExpressionNode(9022) // Wrong type, should evaluate to bool
                                    )
                                ],
                                [
                                    new UnknownNode('invalid-trigger-type')
                                ]
                            )
                        ],
                        'my-invalid-group',
                        new NumberExpressionNode(101) // Wrong type, visibility should evaluate to bool
                    )
                )
            ],
            []
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [app].[page-view].[fixed-static-bag-model].[fixed-static-definition:invalid-attr].[unknown]' .
            ' - Expression is of unknown type "invalid-type-for-page-attr". :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app]' .
            ' - title would get [number], expects [text]. :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group]' .
            ' - visibility would get [number], expects [boolean]. :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group].[defined-widget:0].[unknown-library-for-widget-definition]' .
            ' - Library "invalid-lib" does not exist in order to define widget definition "invalid-widget". :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group].[defined-widget:0].[expression-bag].[unknown]' .
            ' - Expression is of unknown type "invalid-type-for-widget-attr". :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group].[defined-widget:0].[unknown]' .
            ' - [Unknown node] invalid-trigger-type. :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group].[defined-widget:0].[text-widget]' .
            ' - text would get [number], expects [text]. :: ' .

            'ACT node [app].[page-view].[view:my-very-invalid-app].[widget-group:my-invalid-group].[defined-widget:0].[text-widget]' .
            ' - visibility would get [number], expects [boolean]'
        );

        $this->nodeValidator->validate($appNode, $appNode)->throwIfViolated();
    }
}
