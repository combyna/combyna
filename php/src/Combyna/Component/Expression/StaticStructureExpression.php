<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class StaticStructureExpression
 *
 * Represents a list of static attribute values, evaluated from the expressions
 * in a StructureExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureExpression extends AbstractStaticExpression
{
    const TYPE = 'static-structure';

    /**
     * @var StaticBagInterface
     */
    private $staticBag;

    /**
     * @param StaticBagInterface $staticBag
     */
    public function __construct(StaticBagInterface $staticBag)
    {
        $this->staticBag = $staticBag;
    }

    /**
     * Determines whether the attributes in this structure match the provided fixed static bag model,
     * taking into account any attributes that are optional
     *
     * @param DeterminedFixedStaticBagModelInterface $bagModel
     * @return bool
     */
    public function attributesMatch(DeterminedFixedStaticBagModelInterface $bagModel)
    {
        return $bagModel->allowsStaticBag($this->staticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof self &&
            $otherValue->staticBag->equals($this->staticBag);
    }

    /**
     * Fetches an attribute's static from the structure
     *
     * @param string $staticName
     * @return StaticInterface
     */
    public function getAttributeStatic($staticName)
    {
        return $this->staticBag->getStatic($staticName);
    }

    /**
     * Fetches the static bag of attributes in this structure
     *
     * @return StaticBagInterface
     */
    public function getAttributeStaticBag()
    {
        return $this->staticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $staticsWereTruncated = false;
        $summaries = [];

        foreach ($this->staticBag->getStaticNames() as $index => $staticName) {
            $summaries[] = $staticName . ':' . $this->staticBag->getStatic($staticName)->getSummary();

            // Only capture a summary for the first few statics to keep it short-ish
            if ($index > 3) {
                $staticsWereTruncated = true;
                break;
            }
        }

        return sprintf(
            '{%s}',
            // Add an ellipsis to show that we had to truncate the static summaries when applicable
            join(',', $summaries) . ($staticsWereTruncated ? ',...' : '')
        );
    }

    /**
     * Determines whether this structure defines the specified static
     *
     * @param string $staticName
     * @return bool
     */
    public function hasAttributeStatic($staticName)
    {
        return $this->staticBag->hasStatic($staticName);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->staticBag->toNativeArray();
    }

    /**
     * Either creates a new static structure with the specified static value
     * or just returns the current one, if it already has the same static value
     *
     * @param StaticInterface[] $statics
     * @return StaticStructureExpression
     */
    public function withStatics(array $statics)
    {
        $newStaticBag = $this->staticBag->withStatics($statics);

        if ($newStaticBag === $this->staticBag) {
            // Bag already contained all the statics - nothing to do
            return $this;
        }

        return new StaticStructureExpression($newStaticBag);
    }
}
