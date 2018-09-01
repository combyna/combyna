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

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Harness\TestCase;
use InvalidArgumentException;

/**
 * Class ConfigParserTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigParserTest extends TestCase
{
    /**
     * @var ConfigParser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new ConfigParser();
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
        $this->assert($this->parser->getElement($config, $key, $context, $requiredType))
            ->exactlyEquals($expectedResult);
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
        $this->setExpectedException(InvalidArgumentException::class, $expectedException);

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
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Missing required "missing-key" config for my context'
        );

        $this->parser->getElement(['some-key' => 21], 'missing-key', 'my context');
    }
}
