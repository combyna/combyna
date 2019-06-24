<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Validation\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\Exotic\ExoticTypeDeterminerFactoryInterface;
use Combyna\Component\Type\ExoticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\AbstractTypeDeterminer;

/**
 * Class ExoticTypeDeterminer
 *
 * Defines an exotic type.
 * Note that there is also Combyna\Component\Validator\Type\ExoticTypeDeterminer to be used
 * in most scenarios (eg. in an expression ACT node's ->getResultTypeDeterminer() method)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExoticTypeDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'exotic';

    /**
     * @var array
     */
    private $exoticConfig;

    /**
     * @var ExoticTypeDeterminerFactoryInterface
     */
    private $exoticTypeDeterminerFactory;

    /**
     * @var string
     */
    private $exoticTypeDeterminerName;

    /**
     * @param ExoticTypeDeterminerFactoryInterface $exoticTypeDeterminerFactory
     * @param string $exoticTypeDeterminerName
     * @param array $exoticConfig
     */
    public function __construct(
        ExoticTypeDeterminerFactoryInterface $exoticTypeDeterminerFactory,
        $exoticTypeDeterminerName,
        array $exoticConfig
    ) {
        $this->exoticConfig = $exoticConfig;
        $this->exoticTypeDeterminerFactory = $exoticTypeDeterminerFactory;
        $this->exoticTypeDeterminerName = $exoticTypeDeterminerName;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $exoticTypeDeterminer = $this->exoticTypeDeterminerFactory->createDeterminer(
            $this->exoticTypeDeterminerName,
            $this->exoticConfig,
            $validationContext
        );

        return new ExoticType($exoticTypeDeterminer, $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
