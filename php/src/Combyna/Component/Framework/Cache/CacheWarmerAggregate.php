<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Cache;

use Combyna\Component\Common\Cache\CacheWarmerInterface;
use Combyna\Component\Common\Delegator\DelegatorInterface;

/**
 * Class CacheWarmerAggregate
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CacheWarmerAggregate implements CacheWarmerInterface, DelegatorInterface
{
    /**
     * @var CacheWarmerInterface[]
     */
    private $warmers = [];

    /**
     * Adds a new warmer to be delegated to
     *
     * @param CacheWarmerInterface $warmer
     */
    public function addWarmer(CacheWarmerInterface $warmer)
    {
        $this->warmers[] = $warmer;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cachePath)
    {
        foreach ($this->warmers as $warmer) {
            $warmer->warmUp($cachePath);
        }
    }
}
