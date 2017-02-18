<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\ExpressionLanguage;

use Combyna\ExpressionLanguage\ExpressionParser;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Argument;

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
        $this->parser = new ExpressionParser();
    }

    /**
     * @dataProvider expressionToExpectedAstProvider
     * @param string $expression
     * @param array $expectedAst
     */
    public function testParseReturnsExpectedAstForAnExpression($expression, array $expectedAst)
    {
        $this->assert($expectedAst)->equals($this->parser->parse($expression));
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
            'plain number' => [
                '21',
                [
                    'type' => 'number',
                    'number' => 21
                ]
            ],
            'plain string' => [
                '\'Hello world\'',
                [
                    'type' => 'string',
                    'string' => 'Hello world'
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
                    'arguments' => []
                ]
            ],
            'builtin call with one argument' => [
                'my_builtin(only: 101)',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'arguments' => [
                        'only' => [
                            'type' => 'number',
                            'number' => 101
                        ]
                    ]
                ]
            ],
            'builtin call with two arguments' => [
                'my_builtin(first: 27, second: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
                    'arguments' => [
                        'first' => [
                            'type' => 'number',
                            'number' => 27
                        ],
                        'second' => [
                            'type' => 'string',
                            'string' => 'hello'
                        ]
                    ]
                ]
            ],
            'builtin call with three arguments' => [
                'my_builtin(firstArg: 27, secondArg: 100, thirdArg: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
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
                            'type' => 'string',
                            'string' => 'hello'
                        ]
                    ]
                ]
            ],
            'builtin call with expression nested inside argument' => [
                'my_builtin(arg1: 10 * 2, arg2: \'hello\')',
                [
                    'type' => 'builtin',
                    'name' => 'my_builtin',
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
                            'type' => 'string',
                            'string' => 'hello'
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
                            'type' => 'string',
                            'string' => 'hello'
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
                            'type' => 'string',
                            'string' => 'hello'
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
                            'type' => 'string',
                            'string' => 'hello'
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
            'comparing whether two strings are equal, case-sensitively' => [
                '\'hello\' = \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'string',
                        'string' => 'hello'
                    ],
                    'operator' => '=',
                    'right' => [
                        'type' => 'string',
                        'string' => 'world'
                    ]
                ]
            ],
            'comparing whether two strings are equal, case-insensitively' => [
                '\'hello\' ~= \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'string',
                        'string' => 'hello'
                    ],
                    'operator' => '~=',
                    'right' => [
                        'type' => 'string',
                        'string' => 'world'
                    ]
                ]
            ],
            'comparing whether two strings are unequal, case-sensitively' => [
                '\'hello\' <> \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'string',
                        'string' => 'hello'
                    ],
                    'operator' => '<>',
                    'right' => [
                        'type' => 'string',
                        'string' => 'world'
                    ]
                ]
            ],
            'comparing whether two strings are unequal, case-insensitively' => [
                '\'hello\' ~<> \'world\'',
                [
                    'type' => 'comparison',
                    'left' => [
                        'type' => 'string',
                        'string' => 'hello'
                    ],
                    'operator' => '~<>',
                    'right' => [
                        'type' => 'string',
                        'string' => 'world'
                    ]
                ]
            ]
        ];
    }
}
