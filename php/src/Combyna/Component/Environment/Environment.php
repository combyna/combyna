<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment;

use Combyna\Component\Environment\Exception\LibraryAlreadyInstalledException;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Library\LibraryInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class Environment
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Environment implements EnvironmentInterface
{
    /**
     * @var LibraryInterface[]
     */
    private $libraries = [];

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     * @param LibraryInterface[] $libraries
     */
    public function __construct(Translator $translator, array $libraries = [])
    {
        // Index the libraries by name to simplify lookups
        foreach ($libraries as $library) {
            $this->libraries[$library->getName()] = $library;
        }

        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getGenericFunction($libraryName, $functionName)
    {
        if (!array_key_exists($libraryName, $this->libraries)) {
            throw new LibraryNotInstalledException($libraryName);
        }

        return $this->libraries[$libraryName]->getGenericFunction($functionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinition($libraryName, $widgetDefinitionName)
    {
        if (!array_key_exists($libraryName, $this->libraries)) {
            throw new LibraryNotInstalledException($libraryName);
        }

        return $this->libraries[$libraryName]->getWidgetDefinition($widgetDefinitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function installLibrary(LibraryInterface $library)
    {
        if (array_key_exists($library->getName(), $this->libraries)) {
            throw new LibraryAlreadyInstalledException($library->getName());
        }

        $this->libraries[$library->getName()] = $library;

        foreach ($library->getTranslations() as $locale => $messages) {
            $this->translator->addResource('yaml', $messages, $locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function translate($key, array $parameters = [])
    {
        return $this->translator->trans($key, $parameters);
    }
}
