<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Trigger;

use Combyna\Component\Trigger\Exception\TriggerNotFoundException;
use Combyna\Component\Trigger\TriggerCollection;
use Combyna\Component\Trigger\TriggerInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TriggerCollectionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerCollectionTest extends TestCase
{
    /**
     * @var TriggerCollection
     */
    private $collection;

    /**
     * @var ObjectProphecy|TriggerInterface
     */
    private $trigger1;

    /**
     * @var ObjectProphecy|TriggerInterface
     */
    private $trigger2;

    public function setUp()
    {
        $this->trigger1 = $this->prophesize(TriggerInterface::class);
        $this->trigger2 = $this->prophesize(TriggerInterface::class);

        $this->trigger1->getEventLibraryName()
            ->willReturn('first_lib');
        $this->trigger1->getEventName()
            ->willReturn('first_event');

        $this->trigger2->getEventLibraryName()
            ->willReturn('second_lib');
        $this->trigger2->getEventName()
            ->willReturn('second_event');

        $this->collection = new TriggerCollection([
            $this->trigger1->reveal(),
            $this->trigger2->reveal()
        ]);
    }

    public function testGetAllReturnsAllTriggers()
    {
        static::assertSame(
            [$this->trigger1->reveal(), $this->trigger2->reveal()],
            $this->collection->getAll()
        );
    }

    public function testGetByEventNameReturnsAMatchingTrigger()
    {
        static::assertEquals(
            $this->trigger2->reveal(),
            $this->collection->getByEventName('second_lib', 'second_event')
        );
    }

    public function testGetByEventNameThrowsWhenNoSuchTriggerExists()
    {
        $this->expectException(TriggerNotFoundException::class);
        $this->expectExceptionMessage(
            'Collection does not contain a trigger for event "undefined_event" of library "first_lib"'
        );

        $this->collection->getByEventName('first_lib', 'undefined_event');
    }

    public function testHasByEventNameReturnsTrueForAMatchingTrigger()
    {
        static::assertTrue($this->collection->hasByEventName('second_lib', 'second_event'));
    }

    public function testHasByEventNameReturnsFalseWhenNoSuchTriggerExists()
    {
        static::assertFalse($this->collection->hasByEventName('first_lib', 'undefined_event'));
    }

    public function testIsEmptyReturnsTrueForAnEmptyCollection()
    {
        $this->collection = new TriggerCollection([]);

        static::assertTrue($this->collection->isEmpty());
    }

    public function testIsEmptyReturnsFalseForANonEmptyCollection()
    {
        static::assertFalse($this->collection->isEmpty());
    }
}
