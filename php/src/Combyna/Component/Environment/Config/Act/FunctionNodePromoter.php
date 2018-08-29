<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Environment\Exception\NativeFunctionNotInstalledException;
use Combyna\Component\Environment\Library\FunctionCollectionInterface;
use Combyna\Component\Environment\Library\FunctionFactoryInterface;
use Combyna\Component\Environment\Library\FunctionInterface;
use Combyna\Component\Environment\Library\NativeFunction;
use RuntimeException;

/**
 * Class FunctionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var FunctionFactoryInterface
     */
    private $functionFactory;

    /**
     * @param BagNodePromoter $bagNodePromoter
     * @param FunctionFactoryInterface $functionFactory
     */
    public function __construct(
        BagNodePromoter $bagNodePromoter,
        FunctionFactoryInterface $functionFactory
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
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
            return new NativeFunction(
                $functionNode->getName(),
                $this->bagNodePromoter->promoteFixedStaticBagModel($functionNode->getParameterBagModel()),
                $functionNode->getCallable(),
                $functionNode->getReturnType()
            );
        }

        throw new RuntimeException('Only native functions are supported for now');
    }
}
