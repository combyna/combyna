<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Loader;

/**
 * Class QueryCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryCollectionLoader implements QueryCollectionLoaderInterface
{
    /**
     * @var QueryLoaderInterface
     */
    private $queryLoader;

    /**
     * @param QueryLoaderInterface $queryLoader
     */
    public function __construct(QueryLoaderInterface $queryLoader)
    {
        $this->queryLoader = $queryLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $config)
    {
        $queryNodes = [];

        foreach ($config as $queryName => $queryConfig) {
            $queryNodes[$queryName] = $this->queryLoader->load($queryName, $queryConfig);
        }

        return $queryNodes;
    }
}
