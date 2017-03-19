<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Ui\RenderedViewInterface;

/**
 * Interface AppInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppInterface
{
    /**
     * Manually renders the specified view of this app, or returns null if invisible
     *
     * @param string $viewName
     * @param array $viewAttributes
     * @return RenderedViewInterface|null
     */
    public function renderView($viewName, array $viewAttributes = []);
}
