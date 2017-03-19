<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config;

use Combyna\Component\Config\Loader\ArrayWalker;
use Symfony\Component\Yaml\Parser;

/**
 * Class YamlParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class YamlParser
{
    /**
     * @var ArrayWalker
     */
    private $arrayWalker;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param Parser $parser
     * @param ArrayWalker $arrayWalker
     */
    public function __construct(Parser $parser, ArrayWalker $arrayWalker)
    {
        $this->arrayWalker = $arrayWalker;
        $this->parser = $parser;
    }

    /**
     * Parses a YAML config string to an array, processing any embedded expressions
     *
     * @param string $yaml
     * @return array
     */
    public function parse($yaml)
    {
        // First parse the YAML string to an array
        $appConfig = $this->parser->parse($yaml);

        // Walk the array structure to handle expressions (`!expr ...`)
        $appConfig = $this->arrayWalker->walk($appConfig);

        return $appConfig;
    }
}
