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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticDateTimeExpression;
use Combyna\Component\Expression\StaticDayExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Type\StaticType;
use InvalidArgumentException;

/**
 * Class StaticTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var array
     */
    private static $typesToClasses = [
        BooleanExpression::TYPE => BooleanExpression::class,
        NothingExpression::TYPE => NothingExpression::class,
        NumberExpression::TYPE => NumberExpression::class,
        StaticDateTimeExpression::TYPE => StaticDateTimeExpression::class,
        StaticDayExpression::TYPE => StaticDayExpression::class,
        TextExpression::TYPE => TextExpression::class
    ];

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
        $type = $this->configParser->getElement($config, 'type', 'type name');

        if (!array_key_exists($type, self::$typesToClasses)) {
            throw new InvalidArgumentException(
                'Invalid static type "' . $type . '" given - valid types are "' .
                implode('", "', array_keys(self::$typesToClasses)) . '"'
            );
        }

        return new StaticType(self::$typesToClasses[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return array_keys(self::$typesToClasses);
    }
}
