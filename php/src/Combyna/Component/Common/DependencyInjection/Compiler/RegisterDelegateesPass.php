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

use Combyna\Component\Common\Delegator\DelegateeTagDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterDelegateesPass
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterDelegateesPass implements CompilerPassInterface
{
    /**
     * @var DelegateeTagDefinition[]
     */
    private $tagDefinitions = [];

    /**
     * @param DelegateeTagDefinition[] $tagDefinitions
     */
    public function __construct(array $tagDefinitions = [])
    {
        foreach ($tagDefinitions as $tagDefinition) {
            $this->addDelegateeTag($tagDefinition);
        }
    }

    /**
     * Adds a new delegatee tag
     *
     * @param DelegateeTagDefinition $tagDefinition
     */
    public function addDelegateeTag(DelegateeTagDefinition $tagDefinition)
    {
        $this->tagDefinitions[] = $tagDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        foreach ($this->tagDefinitions as $tagDefinition) {
            $tagDefinition->install($containerBuilder);
        }
    }
}
