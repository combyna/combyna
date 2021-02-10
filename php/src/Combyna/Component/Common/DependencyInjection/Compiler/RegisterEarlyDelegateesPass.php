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

use Combyna\Component\Common\Delegator\EarlyDelegateeTagDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterEarlyDelegateesPass
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterEarlyDelegateesPass implements CompilerPassInterface
{
    /**
     * @var EarlyDelegateeTagDefinition[]
     */
    private $tagDefinitions = [];

    /**
     * @param EarlyDelegateeTagDefinition[] $tagDefinitions
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
     * @param EarlyDelegateeTagDefinition $tagDefinition
     */
    public function addDelegateeTag(EarlyDelegateeTagDefinition $tagDefinition)
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
