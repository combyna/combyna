<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\View;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\UiStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Interface ViewStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStateInterface extends UiStateInterface
{
    /**
     * Fetches the bag of attribute statics for this view
     *
     * @return StaticBagInterface
     */
    public function getAttributeStaticBag();

    /**
     * Fetches the state of the root widget for this view
     *
     * @return WidgetStateInterface
     */
    public function getRootWidgetState();

    /**
     * Fetches the state of the store for this view
     *
     * @return ViewStoreStateInterface
     */
    public function getStoreState();

    /**
     * Fetch the unique name of the view
     *
     * @return string
     */
    public function getViewName();

    /**
     * Fetches the path to the widget within this view that is at the specified path.
     * If no widget exists with the given path, a NotFoundException will be thrown instead
     *
     * @param string[]|int[] $path
     * @return WidgetStatePathInterface
     * @throws NotFoundException
     */
    public function getWidgetStatePathByPath(array $path);

    /**
     * Fetches (recursively) the paths to all widgets within this view that have the specified tag
     *
     * @param string $tag
     * @return WidgetStatePathInterface[]
     */
    public function getWidgetStatePathsByTag($tag);
}
