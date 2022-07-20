<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Event;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventDefinitionInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollection;
use Combyna\Component\Event\EventDefinitionReferenceInterface;
use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class EventDefinitionReferenceCollectionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReferenceCollectionTest extends TestCase
{
    /**
     * @var EventDefinitionReferenceCollection
     */
    private $collection;

    /**
     * @var ObjectProphecy|EventDefinitionInterface
     */
    private $definition1;

    /**
     * @var ObjectProphecy|EnvironmentInterface
     */
    private $environment;

    /**
     * @var ObjectProphecy|EventDefinitionReferenceInterface
     */
    private $reference1;

    /**
     * @var ObjectProphecy|EventDefinitionReferenceInterface
     */
    private $reference2;

    public function setUp()
    {
        $this->definition1 = $this->prophesize(EventDefinitionInterface::class);
        $this->environment = $this->prophesize(EnvironmentInterface::class);
        $this->reference1 = $this->prophesize(EventDefinitionReferenceInterface::class);
        $this->reference2 = $this->prophesize(EventDefinitionReferenceInterface::class);

        $this->environment->getEventDefinitionByName('my_lib', 'my_event')
            ->willReturn($this->definition1);

        $this->reference1->getLibraryName()->willReturn('my_lib');
        $this->reference1->getEventName()->willReturn('my_event');

        $this->collection = new EventDefinitionReferenceCollection(
            [$this->reference1->reveal(), $this->reference2->reveal()],
            $this->environment->reveal()
        );
    }

    public function testGetDefinitionByNameFetchesViaEnvironmentWhenReferenceIsInCollection()
    {
        static::assertSame($this->definition1->reveal(), $this->collection->getDefinitionByName('my_lib', 'my_event'));
    }

    public function testGetDefinitionByNameThrowsExceptionWhenReferenceNotInCollection()
    {
        $this->expectException(EventDefinitionNotReferencedException::class);
        $this->expectExceptionMessage(
            'Event definition "an_unsupported_event" for library "any_lib" is not referenced'
        );

        $this->collection->getDefinitionByName('any_lib', 'an_unsupported_event');
    }
}
