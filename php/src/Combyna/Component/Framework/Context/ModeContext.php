<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Context;

use Combyna\Component\Framework\Mode\DevelopmentMode;
use Combyna\Component\Framework\Mode\ModeInterface;
use Combyna\Component\Framework\Mode\ProductionMode;

/**
 * Class ModeContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ModeContext
{
    /**
     * @var ModeInterface
     */
    private $mode;

    public function __construct()
    {
        $this->mode = new DevelopmentMode();
    }

    /**
     * Fetches the current mode
     *
     * @return ModeInterface
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Switches to production mode (non-reversible)
     */
    public function useProductionMode()
    {
        $this->mode = new ProductionMode();
    }
}
