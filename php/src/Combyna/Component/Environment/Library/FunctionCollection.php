<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Environment\Exception\FunctionNotSupportedException;

/**
 * Class FunctionCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionCollection implements FunctionCollectionInterface
{
    /**
     * @var FunctionInterface[]
     */
    private $functions = [];

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param FunctionInterface[] $functions
     * @param string $libraryName
     */
    public function __construct(array $functions, $libraryName)
    {
        // Index the functions by name to simplify lookups
        foreach ($functions as $function) {
            $this->functions[$function->getName()] = $function;
        }

        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($functionName)
    {
        if (!array_key_exists($functionName, $this->functions)) {
            throw new FunctionNotSupportedException($this->libraryName, $functionName);
        }

        return $this->functions[$functionName];
    }
}
