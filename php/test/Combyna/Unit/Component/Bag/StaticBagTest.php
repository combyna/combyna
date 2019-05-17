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
        self::assertSame($this->static2->reveal(), $this->bag->getStatic('second-static'));
    }

    public function testGetStaticThrowsWhenNoStaticExistsInBagWithGivenName()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Static bag contains no "an-undefined-static" static'
        );

        $this->bag->getStatic('an-undefined-static');
    }

    public function testHasStaticReturnsTrueWhenStaticIsDefinedInBag()
    {
        self::assertTrue($this->bag->hasStatic('first-static'));
    }

    public function testHasStaticReturnsFalseWhenStaticIsNotDefinedInBag()
    {
        self::assertFalse($this->bag->hasStatic('an-undefined-static'));
    }

    public function testWithStaticsReturnsTheSameBagWhenProvidedNewStaticsArrayIsEmpty()
    {
        $newBag = $this->bag->withStatics([]);

        self::assertSame($this->bag, $newBag);
    }

    public function testWithStaticsReturnsANewBagWithAllWhenADifferentStaticIsProvided()
    {
        $newStatic = $this->prophesize(StaticInterface::class);

        $newBag = $this->bag->withStatics([
            'new-static' => $newStatic->reveal()
        ]);

        self::assertInstanceOf(StaticBagInterface::class, $newBag);
        self::assertSame($this->static1->reveal(), $newBag->getStatic('first-static'));
        self::assertSame($this->static2->reveal(), $newBag->getStatic('second-static'));
        self::assertSame($newStatic->reveal(), $newBag->getStatic('new-static'));
    }

    public function testWithStaticsGivesPrecedenceToNewStatics()
    {
        $newFirstStatic = $this->prophesize(StaticInterface::class);

        $newBag = $this->bag->withStatics([
            'first-static' => $newFirstStatic->reveal()
        ]);

        self::assertInstanceOf(StaticBagInterface::class, $newBag);
        self::assertSame($newFirstStatic->reveal(), $newBag->getStatic('first-static'));
        self::assertSame($this->static2->reveal(), $newBag->getStatic('second-static'));
    }
}
