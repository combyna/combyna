<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Program;

use Combyna\Component\Plugin\Exception\InvalidLibraryConfigException;
use Combyna\Component\Plugin\Exception\LibraryAlreadyRegisteredException;
use Combyna\Component\Plugin\Exception\MismatchedLibraryNameException;
use Combyna\Component\Plugin\LibraryConfigCollection;
use Combyna\Harness\TestCase;

/**
 * Class LibraryConfigCollectionTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryConfigCollectionTest extends TestCase
{
    /**
     * @var LibraryConfigCollection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new LibraryConfigCollection();
    }

    public function testGetLibraryConfigsFetchesInstalledConfigs()
    {
        $this->collection->addLibraryConfig('my_first_lib', ['name' => 'my_first_lib']);
        $this->collection->addLibraryConfig('my_second_lib', ['name' => 'my_second_lib']);

        static::assertEquals(
            [
                'my_first_lib' => ['name' => 'my_first_lib'],
                'my_second_lib' => ['name' => 'my_second_lib']
            ],
            $this->collection->getLibraryConfigs()
        );
    }

    public function testAddLibraryConfigThrowsWhenNameMissing()
    {
        $this->expectException(InvalidLibraryConfigException::class);
        $this->expectExceptionMessage(
            "Library \"my_lib\" config is invalid due to missing \"name\" value: array (\n  0 => 123,\n)"
        );

        $this->collection->addLibraryConfig('my_lib', [123]);
    }

    public function testAddLibraryConfigThrowsWhenNameDiffersFromNameInConfig()
    {
        $this->expectException(MismatchedLibraryNameException::class);
        $this->expectExceptionMessage('Mismatched "name" value for library "my_lib", "your_lib" given in config');

        $this->collection->addLibraryConfig('my_lib', ['name' => 'your_lib']);
    }

    public function testAddLibraryConfigThrowsWhenLibraryAlreadyInstalled()
    {
        $this->expectException(LibraryAlreadyRegisteredException::class);
        $this->expectExceptionMessage('A library with name "my_lib" was already registered');

        $this->collection->addLibraryConfig('my_lib', ['name' => 'my_lib']);
        $this->collection->addLibraryConfig('my_lib', ['name' => 'my_lib']);
    }
}
