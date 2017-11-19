<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\State;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Common\Exception\NonUniqueResultException;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Interface AppStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppStateInterface
{
    const TYPE = AppNode::TYPE;

    /**
     * Fetches the internal program state from this external app state
     *
     * @return ProgramStateInterface
     */
    public function getProgramState();

    /**
     * Fetches the state of the current page view and any visible overlay views
     *
     * @return ViewStateInterface[]
     */
    public function getVisibleViewStates();

    /**
     * Fetches a single widget path by the path to the widget.
     * If no widget exists with the given path, a NotFoundException will be thrown
     *
     * @param string[]|int[] $path
     * @return WidgetStatePathInterface
     * @throws NotFoundException
     */
    public function getWidgetStatePathByPath(array $path);

    /**
     * Fetches a single widget path by a tag on the widget.
     * If multiple widgets would match, then a NonUniqueResultException will be thrown,
     * but if no widget is found a NotFoundException will be thrown instead
     *
     * @param string $tag
     * @return WidgetStatePathInterface
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function getWidgetStatePathByTag($tag);

    /**
     * Either creates a new app state with the specified program state
     * or just returns the current one, if it already has the same state
     *
     * @param ProgramStateInterface $newProgramState
     * @return AppStateInterface
     */
    public function withProgramState(ProgramStateInterface $newProgramState);
}
