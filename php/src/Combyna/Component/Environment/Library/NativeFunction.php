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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use LogicException;

/**
 * Class NativeFunction
 *
 * Provides a way to define custom functions using native PHP logic
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeFunction implements FunctionInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $parameterBag;

    /**
     * @param string $name
     * @param FixedStaticBagModelInterface $parameterBag A specification for parameters and their types
     * @param callable $callable
     */
    public function __construct(
        $name,
        FixedStaticBagModelInterface $parameterBag,
        callable $callable
    ) {
        $this->callable = $callable;
        $this->name = $name;
        $this->parameterBag = $parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    public function call(StaticBagInterface $argumentStaticBag, TypeInterface $returnType)
    {
        // Sanity check to ensure all arguments match the parameter list before we continue
        $this->parameterBag->assertValidStaticBag($argumentStaticBag);

        $callable = $this->callable;
        $resultStatic = $callable($argumentStaticBag);

        // Functions must return a static as their result
        if (!$resultStatic instanceof StaticInterface) {
            throw new LogicException('Function must return a static, ' . get_class($resultStatic) . ' returned');
        }

        // Check that the function returned a static of the type it declares that it returns
        if (!$returnType->allowsStatic($resultStatic)) {
            throw new LogicException(
                sprintf(
                    'Function "%s" must return a [%s], %s returned',
                    $this->name,
                    $returnType->getSummary(),
                    get_class($resultStatic)
                )
            );
        }

        return $resultStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
