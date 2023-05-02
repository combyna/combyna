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

use Combyna\Component\Program\State\ProgramStateInterface;

/**
 * Class AppState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppState implements AppStateInterface
{
    /**
     * @var ProgramStateInterface
     */
    private $programState;

    /**
     * @param ProgramStateInterface $programState
     */
    public function __construct(ProgramStateInterface $programState)
    {
        $this->programState = $programState;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTitle()
    {
        return $this->programState->getPageViewState()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getProgramState()
    {
        return $this->programState;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouterState()
    {
        return $this->programState->getRouterState();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleViewStates()
    {
        return $this->programState->getVisibleViewStates();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByPath(array $path)
    {
        return $this->programState->getWidgetStatePathByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByTag($tag)
    {
        return $this->programState->getWidgetStatePathByTag($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathsByTag($tag)
    {
        return $this->programState->getWidgetStatePathsByTag($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function withProgramState(ProgramStateInterface $newProgramState)
    {
        if ($this->programState === $newProgramState) {
            // We already have the provided program state, no need to create a new app state
            return $this;
        }

        return new self($newProgramState);
    }
}
