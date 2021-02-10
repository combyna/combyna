<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\ExoticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ExoticTypeDeterminer
 *
 * Defines an exotic type where its exotic type determiner is looked up via the validation context.
 * Note that there is also Combyna\Component\Type\Validation\Type\ExoticTypeDeterminer to be used
 * where an exotic type may need to be built at runtime (eg. by the ExoticTypeLoader)
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
     * @var string
     */
    private $exoticTypeDeterminerName;

    /**
     * @param string $exoticTypeDeterminerName
     * @param array $exoticConfig
     */
    public function __construct($exoticTypeDeterminerName, array $exoticConfig)
    {
        $this->exoticConfig = $exoticConfig;
        $this->exoticTypeDeterminerName = $exoticTypeDeterminerName;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $exoticTypeDeterminer = $validationContext->createExoticTypeDeterminer(
            $this->exoticTypeDeterminerName,
            $this->exoticConfig
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
