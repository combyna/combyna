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
use Combyna\Component\Validator\Context\ValidationContextInterface;
use InvalidArgumentException;

/**
 * Class AdditiveDeterminer
 *
 * Defines a type that is the combination of multiple other types.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AdditiveDeterminer extends AbstractTypeDeterminer
{
    const TYPE = 'additive';

    /**
     * @var TypeDeterminerInterface[]
     */
    private $determiners;

    /**
     * @param TypeDeterminerInterface[] $determiners
     */
    public function __construct(array $determiners)
    {
        if (count($determiners) === 0) {
            throw new InvalidArgumentException('AdditiveDeterminer :: Empty determiners array given');
        }

        $this->determiners = $determiners;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $mergedDeterminedType = $this->determiners[0]->determine($validationContext);

        for ($index = 1; $index < count($this->determiners); $index++) {
            $nextDeterminedType = $this->determiners[$index]->determine($validationContext);

            $mergedDeterminedType = $mergedDeterminedType->mergeWith($nextDeterminedType);
        }

        return $mergedDeterminedType;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        foreach ($this->determiners as $determiner) {
            if ($determiner->makesQuery($querySpecifier)) {
                return true;
            }
        }

        return false;
    }
}
