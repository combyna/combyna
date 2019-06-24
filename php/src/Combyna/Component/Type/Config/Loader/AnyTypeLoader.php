<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Validator\Type\AnyTypeDeterminer;

/**
 * Class AnyTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AnyTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @param ConfigParser $configParser
     */
    public function __construct(ConfigParser $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        return new AnyTypeDeterminer();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['any'];
    }
}
