<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Library\NativeFunction;
use Combyna\Component\Ui\Config\Act\UnknownWidgetDefinitionNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class EnvironmentNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentNode extends AbstractActNode
{
    const TYPE = 'environment';

    /**
     * @var LibraryNode[]
     */
    private $libraryNodes = [];

    /**
     * @param LibraryNode[] $libraryNodes
     */
    public function __construct(array $libraryNodes = [])
    {
        // Index the libraries by name to simplify lookups
        foreach ($libraryNodes as $libraryNode) {
            $this->libraryNodes[$libraryNode->getName()] = $libraryNode;
        }
    }

    /**
     * Fetches a function defined by a library installed into the environment.
     * If the library is not installed then an UnknownLibraryAndFunctionNode will be returned.
     * or if it is but does not define the specified function then an UnknownFunctionNode will be returned
     *
     * @param string $libraryName
     * @param string $functionName
     * @return FunctionNodeInterface
     */
    public function getGenericFunction($libraryName, $functionName)
    {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            return new UnknownLibraryAndFunctionNode($libraryName, $functionName);
        }

        return $this->libraryNodes[$libraryName]->getGenericFunction($functionName);
    }

    /**
     * Fetches all libraries installed into this environment
     *
     * @return LibraryNode[]
     */
    public function getLibraries()
    {
        return $this->libraryNodes;
    }

    /**
     * Fetches a widget definition defined by a library installed into the environment.
     * If the library is not installed or it is but does not define the specified definition,
     * then an UnknownWidgetDefinitionNode will be returned
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionNodeInterface
     */
    public function getWidgetDefinition($libraryName, $widgetDefinitionName)
    {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            return new UnknownWidgetDefinitionNode($libraryName, $widgetDefinitionName);
        }

        return $this->libraryNodes[$libraryName]->getWidgetDefinition($widgetDefinitionName);
    }

    /**
     * Installs a new library into the environment
     *
     * @param LibraryNode $libraryNode
     */
    public function installLibrary(LibraryNode $libraryNode)
    {
        $this->libraryNodes[$libraryNode->getName()] = $libraryNode;
    }

    /**
     * Installs a native function referenced by a NativeFunctionNode
     *
     * @param string $libraryName
     * @param string $functionName
     * @param NativeFunction $nativeFunction
     * @throws LibraryNotInstalledException
     */
    public function installNativeFunction($libraryName, $functionName, NativeFunction $nativeFunction)
    {
        if (!array_key_exists($libraryName, $this->libraryNodes)) {
            throw new LibraryNotInstalledException($libraryName);
        }

        return $this->libraryNodes[$libraryName]->installNativeFunction($functionName, $nativeFunction);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->libraryNodes as $libraryNode) {
            $libraryNode->validate($subValidationContext);
        }
    }
}
