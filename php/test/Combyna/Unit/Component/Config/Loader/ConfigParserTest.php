<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Config\Loader;

use Combyna\Component\Config\Loader\ArgumentParserInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Config\Parameter\ArgumentBagInterface;
use Combyna\Component\Config\Parameter\ParameterInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

/**
 * Class ConfigParserTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigParserTest extends TestCase
{
    /**
     * @var ObjectProphecy|ArgumentParserInterface
     */
    private $argumentParser;

    /**
     * @var ConfigParser
     */
    private $parser;

    public function setUp()
    {
        $this->argumentParser = $this->prophesize(ArgumentParserInterface::class);

        $this->parser = new ConfigParser($this->argumentParser->reveal());
    }

    /**
     * @dataProvider dataProviderForGetElementReturnsValueWhenExpected
     * @param array $config
     * @param string $key
     * @param string $context
     * @param string $requiredType
     * @param mixed $expectedResult
     */
    public function testGetElementReturnsValueWhenExpected(
        $config,
        $key,
        $context,
        $requiredType,
        $expectedResult
    ) {
        static::assertSame($expectedResult, $this->parser->getElement($config, $key, $context, $requiredType));
    }

    /**
     * @return array
     */
    public function dataProviderForGetElementReturnsValueWhenExpected()
    {
        return [
            'fetching a string element' => [
                ['my-element' => 'my string'],
                'my-element',
                'some context of this fetch',
                'string',

                'my string'
            ],

            'fetching an int element when an int is expected' => [
                ['my-element' => 21],
                'my-element',
                'some context of this fetch',
                'integer',

                21
            ],

            'fetching an int element when any number is expected' => [
                ['my-element' => 101],
                'my-element',
                'some context of this fetch',
                'number',

                101
            ],

            'fetching a double (float) element when a double is expected' => [
                ['my-element' => 27.2],
                'my-element',
                'some context of this fetch',
                'double',

                27.2
            ],

            'fetching a double (float) element when any number expected' => [
                ['my-element' => 27.2],
                'my-element',
                'some context of this fetch',
                'number',

                27.2
            ],

            'fetching a boolean element' => [
                ['my-element' => true],
                'my-element',
                'some context of this fetch',
                'boolean',

                true
            ],

            'fetching a boolean element when type could be string or boolean' => [
                ['my-element' => true],
                'my-element',
                'some context of this fetch',
                ['string', 'boolean'],

                true // Expected boolean
            ],

            'fetching a string element when type could be string or boolean' => [
                ['my-element' => 'my value'],
                'my-element',
                'some context of this fetch',
                ['string', 'boolean'],

                'my value' // Expected string
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForGetElementThrowsMismatchExceptionWhenExpected
     * @param array $config
     * @param string $key
     * @param string $context
     * @param string $requiredType
     * @param string $expectedException
     */
    public function testGetElementThrowsMismatchExceptionWhenExpected(
        $config,
        $key,
        $context,
        $requiredType,
        $expectedException
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedException);

        $this->parser->getElement($config, $key, $context, $requiredType);
    }

    /**
     * @return array
     */
    public function dataProviderForGetElementThrowsMismatchExceptionWhenExpected()
    {
        return [
            'fetching a string element when type could be array or boolean' => [
                ['my-element' => 'my value'],
                'my-element',
                'some context of this fetch',
                ['array', 'boolean'],

                'Config element "my-element" should be of one of the type(s) ["array", "boolean"] ' .
                'but is "string" for some context of this fetch'
            ]
        ];
    }

    public function testGetElementThrowsExceptionWhenElementIsMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing required "missing-key" config for my context'
        );

        $this->parser->getElement(['some-key' => 21], 'missing-key', 'my context');
    }

    /**
     * @dataProvider dataProviderForGetOptionalElementReturnsValueWhenExpected
     * @param array $config
     * @param string $key
     * @param string $context
     * @param mixed $defaultValue
     * @param string $requiredType
     * @param mixed $expectedResult
     */
    public function testGetOptionalElementReturnsValueWhenExpected(
        $config,
        $key,
        $context,
        $defaultValue,
        $requiredType,
        $expectedResult
    ) {
        static::assertSame($expectedResult, $this->parser->getOptionalElement($config, $key, $context, $defaultValue, $requiredType));
    }

    /**
     * @return array
     */
    public function dataProviderForGetOptionalElementReturnsValueWhenExpected()
    {
        return [
            'fetching a string element that exists' => [
                ['my-element' => 'my string'],
                'my-element',
                'some context of this fetch',
                'my default value',
                'string',

                'my string'
            ],

            'fetching a string element that doesn\'t exist' => [
                ['not-my-element' => 'not my string'],
                'my-element',
                'some context of this fetch',
                'my default value',
                'string',

                'my default value'
            ],

            'fetching an int element that exists when an int is expected' => [
                ['my-element' => 21],
                'my-element',
                'some context of this fetch',
                101, // Default value
                'integer',

                21
            ],

            'fetching an int element that exists when any number is expected' => [
                ['my-element' => 101],
                'my-element',
                'some context of this fetch',
                901, // Default value
                'number',

                101
            ],

            'fetching a double (float) element that exists when a double is expected' => [
                ['my-element' => 27.2],
                'my-element',
                'some context of this fetch',
                101.4, // Default value
                'double',

                27.2
            ],

            'fetching a double (float) element that exists when any number expected' => [
                ['my-element' => 27.2],
                'my-element',
                'some context of this fetch',
                99.7, // Default value
                'number',

                27.2
            ],

            'fetching a boolean element that exists' => [
                ['my-element' => true],
                'my-element',
                'some context of this fetch',
                false, // Default value
                'boolean',

                true
            ],

            'fetching a boolean element that exists when type could be string or boolean' => [
                ['my-element' => true],
                'my-element',
                'some context of this fetch',
                true, // Default value
                ['string', 'boolean'],

                true // Expected boolean
            ],

            'fetching a string element that exists when type could be string or boolean' => [
                ['my-element' => 'my value'],
                'my-element',
                'some context of this fetch',
                'my default value',
                ['string', 'boolean'],

                'my value' // Expected string
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForGetOptionalElementThrowsMismatchExceptionWhenExpected
     * @param array $config
     * @param string $key
     * @param string $context
     * @param string $defaultValue
     * @param string $requiredType
     * @param string $expectedException
     */
    public function testGetOptionalElementThrowsMismatchExceptionWhenExpected(
        $config,
        $key,
        $context,
        $defaultValue,
        $requiredType,
        $expectedException
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedException);

        $this->parser->getOptionalElement($config, $key, $context, $defaultValue, $requiredType);
    }

    /**
     * @return array
     */
    public function dataProviderForGetOptionalElementThrowsMismatchExceptionWhenExpected()
    {
        return [
            'fetching a string element when type could be array or boolean' => [
                ['my-element' => 'my value'],
                'my-element',
                'some context of this fetch',
                true,
                ['array', 'boolean'],

                'Config element "my-element" should be of one of the type(s) ["array", "boolean"] ' .
                'but is "string" for some context of this fetch'
            ]
        ];
    }

    public function testParseArgumentsDelegatesToArgumentParserAndReturnsResultCorrectly()
    {
        $argumentBag = $this->prophesize(ArgumentBagInterface::class);
        $parameter1 = $this->prophesize(ParameterInterface::class);
        $parameter2 = $this->prophesize(ParameterInterface::class);
        $this->argumentParser->parseArguments(
            [
                ArgumentParserInterface::NAMED_ARGUMENTS => ['my' => 'config']
            ],
            [
                $parameter1->reveal(),
                $parameter2->reveal()
            ]
        )->willReturn($argumentBag);

        static::assertSame(
            $argumentBag->reveal(),
            $this->parser->parseArguments(
                ['my' => 'config'],
                [
                    $parameter1->reveal(),
                    $parameter2->reveal()
                ]
            )
        );
    }

    public function testToArrayReturnsAnArrayPassedIn()
    {
        static::assertSame([21, 'my_key' => 'my value'], $this->parser->toArray([21, 'my_key' => 'my value']));
    }

    public function testToArrayReturnsAnEmptyArrayWhenNullGiven()
    {
        static::assertSame([], $this->parser->toArray(null));
    }

    /**
     * @dataProvider toArrayThrowsWhenNonArrayAndNonNullGiven_dataProvider
     * @param mixed $value
     * @param string $type
     */
    public function testToArrayThrowsWhenNonArrayAndNonNullGiven($value, $type)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Config should be null or array but is of type "' . $type . '"'
        );

        $this->parser->toArray($value);
    }

    /**
     * @return array
     */
    public function toArrayThrowsWhenNonArrayAndNonNullGiven_dataProvider()
    {
        return [
            'integer' => [21, 'integer'],
            'float' => [101.456, 'double'],
            'object' => [new stdClass(), 'object'],
            'string' => ['some string', 'string']
        ];
    }
}
