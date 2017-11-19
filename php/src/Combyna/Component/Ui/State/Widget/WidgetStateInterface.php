<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\State\StateInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\UiStateInterface;

/**
 * Interface WidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetStateInterface extends UiStateInterface
{
    /**
     * Fetches the name of the library this widget's definition belongs to
     *
     * @return string
     */
    public function getWidgetDefinitionLibraryName();

    /**
     * Fetches the unique name of the definition for this widget
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Fetches the path to the widget this state is for, with its view name and all ancestor state names
     *
     * NB: that this may be slightly different to the widget state's path,
     *     when eg. an ancestor is a Repeater
     *
     * @return string[]
     */
    public function getWidgetPath();

    /**
     * Fetches the path to the descendant widget (if any) that is at the specified path.
     * If no widget exists with the given path, a NotFoundException will be thrown instead
     *
     * @param string[]|int[] $path
     * @param StateInterface[] $parentStates
     * @param UiStateFactoryInterface $stateFactory
     * @return WidgetStatePathInterface
     * @throws NotFoundException
     */
    public function getWidgetStatePathByPath(array $path, array $parentStates, UiStateFactoryInterface $stateFactory);

    /**
     * Fetches (recursively) the paths to all descendent widget states including this one that have the specified tag
     *
     * @param string $tag
     * @param StateInterface[] $parentStates
     * @param UiStateFactoryInterface $stateFactory
     * @return WidgetStatePathInterface[]
     */
    public function getWidgetStatePathsByTag($tag, array $parentStates, UiStateFactoryInterface $stateFactory);
}
