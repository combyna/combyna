<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

/**
 * Class NativeFunctionLocator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeFunctionLocator implements NativeFunctionLocatorInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string[]
     */
    private $parameterNamesInArgumentOrder;

    /**
     * @param string $libraryName
     * @param string $functionName
     * @param callable $callable
     * @param string[] $parameterNamesInArgumentOrder
     */
    public function __construct(
        $libraryName,
        $functionName,
        callable $callable,
        array $parameterNamesInArgumentOrder
    ) {
        $this->callable = $callable;
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
        $this->parameterNamesInArgumentOrder = $parameterNamesInArgumentOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterNamesInArgumentOrder()
    {
        return $this->parameterNamesInArgumentOrder;
    }
}
