<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Environment\Exception\NativeFunctionNotInstalledException;
use Combyna\Component\Environment\Library\FunctionCollectionInterface;
use Combyna\Component\Environment\Library\FunctionFactoryInterface;
use Combyna\Component\Environment\Library\FunctionInterface;
use RuntimeException;

/**
 * Class FunctionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionNodePromoter
{
    /**
     * @var FunctionFactoryInterface
     */
    private $functionFactory;

    /**
     * @param FunctionFactoryInterface $functionFactory
     */
    public function __construct(FunctionFactoryInterface $functionFactory)
    {
        $this->functionFactory = $functionFactory;
    }

    /**
     * Creates a FunctionCollection from FunctionNodes
     *
     * @param FunctionNodeInterface[] $functionNodes
     * @param string $libraryName
     * @return FunctionCollectionInterface
     */
    public function promoteCollection(array $functionNodes, $libraryName)
    {
        $functions = [];

        foreach ($functionNodes as $functionNode) {
            $functions[] = $this->promoteFunction($functionNode);
        }

        return $this->functionFactory->createCollection($functions, $libraryName);
    }

    /**
     * Creates a Function from its ACT node
     *
     * @param FunctionNodeInterface $functionNode
     * @return FunctionInterface
     * @throws NativeFunctionNotInstalledException
     */
    public function promoteFunction(FunctionNodeInterface $functionNode)
    {
        if ($functionNode instanceof NativeFunctionNode) {
            return $functionNode->getNativeFunction();
        }

        throw new RuntimeException('Only native functions are supported for now');
    }
}
