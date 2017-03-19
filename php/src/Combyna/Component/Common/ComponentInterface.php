<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Interface ComponentInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ComponentInterface
{
    /**
     * Fetches the Symfony container extension defined for this component, if any
     *
     * @return ExtensionInterface|null
     */
    public function getContainerExtension();

    /**
     * Fetches the short/unqualified class name for this component, without `Component` suffix.
     * For example, the component class `Combyna\Component\Awesome\AwesomeComponent` would have
     * the name `Awesome`
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the namespace prefix for this component.
     * For example, the component class `Combyna\Component\Awesome\AwesomeComponent` would have
     * the namespace `Combyna\Component\Awesome`
     *
     * @return string
     */
    public function getNamespace();
}
