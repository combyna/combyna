<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use InvalidArgumentException;

/**
 * Class DelegatingExoticTypeDeterminerFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExoticTypeDeterminerFactory implements ExoticTypeDeterminerFactoryInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $determinerFactories = [];

    /**
     * Adds a factory for a new kind of determiner
     *
     * @param ExoticTypeTypeDeterminerFactoryInterface $determinerFactory
     */
    public function addFactory(ExoticTypeTypeDeterminerFactoryInterface $determinerFactory)
    {
        foreach ($determinerFactory->getTypeNameToFactoryCallableMap() as $typeName => $factoryCallable) {
            $this->determinerFactories[$typeName] = $factoryCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDeterminer(
        $determinerName,
        array $config,
        ValidationContextInterface $sourceValidationContext
    ) {
        if (!array_key_exists($determinerName, $this->determinerFactories)) {
            throw new InvalidArgumentException(sprintf(
                'No factory for exotic type determiners of type "%s" is registered',
                $determinerName
            ));
        }

        return $this->determinerFactories[$determinerName]($config, $sourceValidationContext);
    }
}
