<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\ExpressionLanguage;

use Combyna\Component\ExpressionLanguage\ExpressionParser;
use Combyna\Harness\TestCase;
use InvalidArgumentException;

/**
 * Class ExpressionParserTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionParserTest extends TestCase
{
    /**
     * @var ExpressionParser
     */
    private $parser;

    public function setUp()
    {
        global $combynaBootstrap;

        $this->parser = new ExpressionParser($combynaBootstrap->getCommonCachePath());
    }

    /**
     * @dataProvider expressionToExpectedAstProvider
     * @param string $expression
     * @param array $expectedAst
     */
    public function testParseReturnsExpectedAstForAnExpression($expression, array $expectedAst)
    {
        $this->assert($this->parser->parse($expression))->equals($expectedAst);
    }

    public function testParseReturnsNativeIntegerForIntegerLiterals()
    {
        $this->assert($this->parser->parse('21'))->exactlyEquals([
            'type' => 'number',
            'number' => 21
        ]);
    }

    public function testParseReturnsNativeFloatForFloatLiterals()
    {
        $this->assert($this->parser->parse('199.1234'))->exactlyEquals([
            'type' => 'number',
            'number' => 199.1234
        ]);
    }

    public function testParseThrowsExceptionWhenUnableToParseExpression()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Could not parse expression string "@@@!! [Not] a valid - expression !!"'
        );

        $this->parser->parse('@@@!! [Not] a valid - expression !!');
    }

    /**
     * @return array
     */
    public function expressionToExpectedAstProvider()
    {
        return [
            'plain integer' => [
                '21',
                [
                    'type' => 'number',
                    'number' => 21
                ]
            ],
            'plain float' => [
                '101.123',
                [
                    'type' => 'number',
                    'number' => 101.123
                ]
            ],
            'plain text' => [
                '\'Hello world\'',
                [
                    'type' => 'text',
                    'text' => 'Hello world'
                ]
            ],
            'plain boolean true' => [
                'true',
                [
                    'type' => 'boolean',
                    'boolean' => true
                ]
            ],
            'plain boolean false' => [
                'false',
                [
                    'type' => 'boolean',
                    'boolean' => false
                ]
            ],
            'plain variable reference' => [
                'my_var',
                [
                    'type' => 'variable',
                    'variable' => 'my_var'
                ]
            ],
            'empty list expression' => [
                '[]',
                [
                    'type' => 'list',
                    'elements' => []
                ]
            ],
            'plain list expression' => [
                '[21, \'my text\', 101]',
                [
                    'type' => 'list',
                    'elements' => [
                        [
                            'type' => 'number',
                            'number' => 21
                        ],
                        [
                            'type' => 'text',
                            'text' => 'my text'
                        ],
                        [
                            'type' => 'number',
                            'number' => 101
                        ]
                    ]
                ]
            ],
            'adding two numbers' => [
                '12 + 4',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 12
                    ],
                    'operator' => '+',
                    'right' => [
                        'type' => 'number',
                        'number' => 4
                    ]
                ]
            ],
            'adding two numbers across multiple lines' => [
                "12\n \n+\n \n4",
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 12
                    ],
                    'operator' => '+',
                    'right' => [
                        'type' => 'number',
                        'number' => 4
                    ]
                ]
            ],
            'adding three numbers' => [
                '12 + 10 + 7',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'number',
                            'number' => 12
                        ],
                        'operator' => '+',
                        'right' => [
                            'type' => 'number',
                            'number' => 10
                        ]
                    ],
                    'operator' => '+',
                    'right' => [
                        'type' => 'number',
                        'number' => 7
                    ]
                ]
            ],
            'subtracting two numbers' => [
                '27 - 7',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 27
                    ],
                    'operator' => '-',
                    'right' => [
                        'type' => 'number',
                        'number' => 7
                    ]
                ]
            ],
            'multiplying two numbers' => [
                '10 * 2',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 10
                    ],
                    'operator' => '*',
                    'right' => [
                        'type' => 'number',
                        'number' => 2
                    ]
                ]
            ],
            'dividing two numbers' => [
                '30 / 3',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 30
                    ],
                    'operator' => '/',
                    'right' => [
                        'type' => 'number',
                        'number' => 3
                    ]
                ]
            ],
            'adding a number to a variable' => [
                'a_var + 21',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'variable',
                        'variable' => 'a_var'
                    ],
                    'operator' => '+',
                    'right' => [
                        'type' => 'number',
                        'number' => 21
                    ]
                ]
            ],
            'overriding operator precedence with parentheses' => [
                'first_var * (second_var + 4)',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'variable',
                        'variable' => 'first_var'
                    ],
                    'operator' => '*',
                    'right' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'variable',
                            'variable' => 'second_var'
                        ],
                        'operator' => '+',
                        'right' => [
                            'type' => 'number',
                            'number' => 4
                        ]
                    ]
                ]
            ],
            'all arithmetic operations nested' => [
                '1 + 2 - 3 * 4 / 5',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'number',
                            'number' => 1
                        ],
                        'operator' => '+',
                        'right' => [
                            'type' => 'number',
                            'number' => 2
                        ]
                    ],
                    'operator' => '-',
                    'right' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'binary-arithmetic',
                            'left' => [
                                'type' => 'number',
                                'number' => 3
                            ],
                            'operator' => '*',
                            'right' => [
                                'type' => 'number',
                                'number' => 4
                            ]
                        ],
                        'operator' => '/',
                        'right' => [
                            'type' => 'number',
                            'number' => 5
                        ]
                    ]
                ]
            ],
            'builtin call with no arguments' => [
                'my_builtin()',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [],
                    'named-arguments' => []
                ]
            ],
            'builtin call with one named argument' => [
                'my_builtin(only: 101)',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [],
                    'named-arguments' => [
                        'only' => [
                            'type' => 'number',
                            'number' => 101
                        ]
                    ]
                ]
            ],
            'builtin call with one positional argument' => [
                'my_builtin(\'my stuff\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [
                        [
                            'type' => 'text',
                            'text' => 'my stuff'
                        ]
                    ],
                    'named-arguments' => []
                ]
            ],
            'builtin call with one positional argument passing a list' => [
                'my_builtin([21])',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [
                        [
                            'type' => 'list',
                            'elements' => [
                                [
                                    'type' => 'number',
                                    'number' => 21
                                ]
                            ]
                        ]
                    ],
                    'named-arguments' => []
                ]
            ],
            'builtin call with two positional and two named arguments' => [
                'my_builtin(101, 9999, first: 27, second: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [
                        [
                            'type' => 'number',
                            'number' => 101
                        ],
                        [
                            'type' => 'number',
                            'number' => 9999
                        ]
                    ],
                    'named-arguments' => [
                        'first' => [
                            'type' => 'number',
                            'number' => 27
                        ],
                        'second' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'builtin call with three named arguments' => [
                'my_builtin(firstArg: 27, secondArg: 100, thirdArg: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [],
                    'named-arguments' => [
                        'firstArg' => [
                            'type' => 'number',
                            'number' => 27
                        ],
                        'secondArg' => [
                            'type' => 'number',
                            'number' => 100
                        ],
                        'thirdArg' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'builtin call with expression nested inside named arguments' => [
                'my_builtin(arg1: 10 * 2, arg2: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'positional-arguments' => [],
                    'named-arguments' => [
                        'arg1' => [
                            'type' => 'binary-arithmetic',
                            'left' => [
                                'type' => 'number',
                                'number' => 10
                            ],
                            'operator' => '*',
                            'right' => [
                                'type' => 'number',
                                'number' => 2
                            ]
                        ],
                        'arg2' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'function call with no arguments' => [
                'my_lib.my_func()',
                [
                    'type' => 'function',
                    'library' => 'my_lib',
                    'name' => 'my_func',
                    'arguments' => []
                ]
            ],
            'function call with one argument' => [
                'my_lib.my_func(only: 101)',
                [
                    'type' => 'function',
                    'library' => 'my_lib',
                    'name' => 'my_func',
                    'arguments' => [
                        'only' => [
                            'type' => 'number',
                            'number' => 101
                        ]
                    ]
                ]
            ],
            'function call with two arguments' => [
                'my_lib.my_func(first: 27, second: \'hello\')',
                [
                    'type' => 'function',
                    'library' => 'my_lib',
                    'name' => 'my_func',
                    'arguments' => [
                        'first' => [
                            'type' => 'number',
                            'number' => 27
                        ],
                        'second' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'function call with three arguments' => [
                'my_lib.my_func(firstArg: 27, secondArg: 100, thirdArg: \'hello\')',
                [
                    'type' => 'function',
                    'library' => 'my_lib',
                    'name' => 'my_func',
                    'arguments' => [
                        'firstArg' => [
                            'type' => 'number',
                            'number' => 27
                        ],
                        'secondArg' => [
                            'type' => 'number',
                            'number' => 100
                        ],
                        'thirdArg' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'function call with expression nested inside argument' => [
                'my_lib.my_func(arg1: 10 * 2, arg2: \'hello\')',
                [
                    'type' => 'function',
                    'library' => 'my_lib',
                    'name' => 'my_func',
                    'arguments' => [
                        'arg1' => [
                            'type' => 'binary-arithmetic',
                            'left' => [
                                'type' => 'number',
                                'number' => 10
                            ],
                            'operator' => '*',
                            'right' => [
                                'type' => 'number',
                                'number' => 2
                            ]
                        ],
                        'arg2' => [
                            'type' => 'text',
                            'text' => 'hello'
                        ]
                    ]
                ]
            ],
            'comparing whether two numbers are equal' => [
                '21 = 4',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'number',
                        'number' => 21
                    ],
                    'operator' => '=',
                    'right' => [
                        'type' => 'number',
                        'number' => 4
                    ]
                ]
            ],
            'comparing whether two texts are equal, case-sensitively' => [
                '\'hello\' = \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'text',
                        'text' => 'hello'
                    ],
                    'operator' => '=',
                    'right' => [
                        'type' => 'text',
                        'text' => 'world'
                    ]
                ]
            ],
            'comparing whether two texts are equal, case-insensitively' => [
                '\'hello\' ~= \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'text',
                        'text' => 'hello'
                    ],
                    'operator' => '~=',
                    'right' => [
                        'type' => 'text',
                        'text' => 'world'
                    ]
                ]
            ],
            'comparing whether two texts are unequal, case-sensitively' => [
                '\'hello\' <> \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'text',
                        'text' => 'hello'
                    ],
                    'operator' => '<>',
                    'right' => [
                        'type' => 'text',
                        'text' => 'world'
                    ]
                ]
            ],
            'comparing whether two texts are unequal, case-insensitively' => [
                '\'hello\' ~<> \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'text',
                        'text' => 'hello'
                    ],
                    'operator' => '~<>',
                    'right' => [
                        'type' => 'text',
                        'text' => 'world'
                    ]
                ]
            ],
            'concatenating a variable with a string (static concatenation)' => [
                'my_prefix_string ~ \' my suffix\'',
                [
                    'type' => 'concatenation',
                    'list' => [
                        // List operand is implicit in this static version
                        'type' => 'list',
                        'elements' => [
                            [
                                'type' => 'variable',
                                'variable' => 'my_prefix_string'
                            ],
                            [
                                'type' => 'text',
                                'text' => ' my suffix'
                            ]
                        ]
                    ]
                ]
            ],
            'concatenating a sequence of expressions (static concatenation)' => [
                'my_prefix_string ~ 21.2 ~ \' my suffix\'',
                [
                    'type' => 'concatenation',
                    'list' => [
                        // List operand is implicit in this static version
                        'type' => 'list',
                        'elements' => [
                            [
                                'type' => 'variable',
                                'variable' => 'my_prefix_string'
                            ],
                            [
                                'type' => 'number',
                                'number' => 21.2
                            ],
                            [
                                'type' => 'text',
                                'text' => ' my suffix'
                            ]
                        ]
                    ]
                ]
            ],
            'bare conditional operator' => [
                'my_condition = 21 ? my_consequent + 2 : my_alternate * 5',
                [
                    'type' => 'conditional',
                    'condition' => [
                        'type' => 'comparison',
                        'left' => [
                            'type' => 'variable',
                            'variable' => 'my_condition'
                        ],
                        'operator' => '=',
                        'right' => [
                            'type' => 'number',
                            'number' => 21
                        ]
                    ],
                    'consequent' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'variable',
                            'variable' => 'my_consequent'
                        ],
                        'operator' => '+',
                        'right' => [
                            'type' => 'number',
                            'number' => 2
                        ]
                    ],
                    'alternate' => [
                        'type' => 'binary-arithmetic',
                        'left' => [
                            'type' => 'variable',
                            'variable' => 'my_alternate'
                        ],
                        'operator' => '*',
                        'right' => [
                            'type' => 'number',
                            'number' => 5
                        ]
                    ]
                ]
            ],
            'nested conditional operator inside calculation (precedence test)' => [
                '1111 + (outer_condition = 21 ? inner_condition = 27 ? my_consequent + 2 : first_alternate * 5 : second_alternate * 10)',
                [
                    'type' => 'binary-arithmetic',
                    'left' => [
                        'type' => 'number',
                        'number' => 1111
                    ],
                    'operator' => '+',
                    'right' => [
                        'type' => 'conditional',
                        'condition' => [
                            'type' => 'comparison',
                            'left' => [
                                'type' => 'variable',
                                'variable' => 'outer_condition'
                            ],
                            'operator' => '=',
                            'right' => [
                                'type' => 'number',
                                'number' => 21
                            ]
                        ],
                        'consequent' => [
                            'type' => 'conditional',
                            'condition' => [
                                'type' => 'comparison',
                                'left' => [
                                    'type' => 'variable',
                                    'variable' => 'inner_condition'
                                ],
                                'operator' => '=',
                                'right' => [
                                    'type' => 'number',
                                    'number' => 27
                                ]
                            ],
                            'consequent' => [
                                'type' => 'binary-arithmetic',
                                'left' => [
                                    'type' => 'variable',
                                    'variable' => 'my_consequent'
                                ],
                                'operator' => '+',
                                'right' => [
                                    'type' => 'number',
                                    'number' => 2
                                ]
                            ],
                            'alternate' => [
                                'type' => 'binary-arithmetic',
                                'left' => [
                                    'type' => 'variable',
                                    'variable' => 'first_alternate'
                                ],
                                'operator' => '*',
                                'right' => [
                                    'type' => 'number',
                                    'number' => 5
                                ]
                            ]
                        ],
                        'alternate' => [
                            'type' => 'binary-arithmetic',
                            'left' => [
                                'type' => 'variable',
                                'variable' => 'second_alternate'
                            ],
                            'operator' => '*',
                            'right' => [
                                'type' => 'number',
                                'number' => 10
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
