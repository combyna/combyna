<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SequentialPass
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SequentialPass implements CompilerPassInterface
{
    /**
     * @var CompilerPassInterface[]
     */
    private $subPasses;

    /**
     * @param CompilerPassInterface[] $subPasses
     */
    public function __construct(array $subPasses)
    {
        $this->subPasses = $subPasses;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        foreach ($this->subPasses as $subPass) {
            $subPass->process($containerBuilder);
        }
    }
}
