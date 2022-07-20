<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Bag;

use Combyna\Component\Bag\StaticBag;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticBagTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticBagTest extends TestCase
{
    /**
     * @var StaticBag
     */
    private $bag;

    /**
     * @var ObjectProphecy|StaticInterface
     */
    private $static1;

    /**
     * @var ObjectProphecy|StaticInterface
     */
    private $static2;

    public function setUp()
    {
        $this->static1 = $this->prophesize(StaticInterface::class);
        $this->static2 = $this->prophesize(StaticInterface::class);

        $this->bag = new StaticBag([
            'first-static' => $this->static1->reveal(),
            'second-static' => $this->static2->reveal()
        ]);
    }

    public function testGetStaticReturnsTheCorrectStatic()
    {
        static::assertSame($this->static2->reveal(), $this->bag->getStatic('second-static'));
    }

    public function testGetStaticThrowsWhenNoStaticExistsInBagWithGivenName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Static bag contains no "an-undefined-static" static'
        );

        $this->bag->getStatic('an-undefined-static');
    }

    public function testGetStaticNamesReturnsTheNames()
    {
        static::assertEquals(['first-static', 'second-static'], $this->bag->getStaticNames());
    }

    public function testHasStaticReturnsTrueWhenStaticIsDefinedInBag()
    {
        static::assertTrue($this->bag->hasStatic('first-static'));
    }

    public function testHasStaticReturnsFalseWhenStaticIsNotDefinedInBag()
    {
        static::assertFalse($this->bag->hasStatic('an-undefined-static'));
    }

    public function testWithStaticsReturnsTheSameBagWhenProvidedNewStaticsArrayIsEmpty()
    {
        $newBag = $this->bag->withStatics([]);

        static::assertSame($this->bag, $newBag);
    }

    public function testWithStaticsReturnsANewBagWithAllWhenADifferentStaticIsProvided()
    {
        $newStatic = $this->prophesize(StaticInterface::class);

        $newBag = $this->bag->withStatics([
            'new-static' => $newStatic->reveal()
        ]);

        static::assertInstanceOf(StaticBagInterface::class, $newBag);
        static::assertSame($this->static1->reveal(), $newBag->getStatic('first-static'));
        static::assertSame($this->static2->reveal(), $newBag->getStatic('second-static'));
        static::assertSame($newStatic->reveal(), $newBag->getStatic('new-static'));
    }

    public function testWithStaticsGivesPrecedenceToNewStatics()
    {
        $newFirstStatic = $this->prophesize(StaticInterface::class);

        $newBag = $this->bag->withStatics([
            'first-static' => $newFirstStatic->reveal()
        ]);

        static::assertInstanceOf(StaticBagInterface::class, $newBag);
        static::assertSame($newFirstStatic->reveal(), $newBag->getStatic('first-static'));
        static::assertSame($this->static2->reveal(), $newBag->getStatic('second-static'));
    }
}
