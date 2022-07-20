<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Event\Exception;

use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Harness\TestCase;

/**
 * Class EventDefinitionNotReferencedExceptionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNotReferencedExceptionTest extends TestCase
{
    /**
     * @var EventDefinitionNotReferencedException
     */
    private $exception;

    public function setUp()
    {
        $this->exception = new EventDefinitionNotReferencedException('my_lib', 'my_event');
    }

    public function testGetEventDefinitionNameReturnsTheDefinitionName()
    {
        static::assertSame('my_event', $this->exception->getEventDefinitionName());
    }

    public function testGetLibraryNameReturnsTheLibraryName()
    {
        static::assertSame('my_lib', $this->exception->getLibraryName());
    }

    public function testGetMessageReturnsTheCorrectMessage()
    {
        static::assertSame('Event definition "my_event" for library "my_lib" is not referenced', $this->exception->getMessage());
    }
}
