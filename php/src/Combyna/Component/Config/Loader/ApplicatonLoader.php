<?php

namespace Combyna\Component\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class ApplicatonLoader
 * @package Combyna\Component\Config\Loader
 */
class ApplicatonLoader extends FileLoader
{
    /**
     * @var ConfigBuilder
     */
    private $configBuilder;

    /**
     * ApplicatonLoader constructor.
     * @param ConfigBuilder $configBuilder
     * @param FileLocatorInterface $locator
     */
    public function __construct(ConfigBuilder $configBuilder, FileLocatorInterface $locator)
    {
        parent::__construct($locator);
        $this->configBuilder = $configBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function load($file, $type = null)
    {
        $file = rtrim($file, '/');
        $path = $this->locator->locate($file);

        foreach (scandir($path) as $dir) {
            if ('.' !== $dir[0]) {
                if (is_dir($path.'/'.$dir)) {
                    $dir .= '/'; // append / to allow recursion
                }

                $this->setCurrentDir($path);

                $this->import($dir, null, false, $path);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        if ('directory' === $type) {
            return true;
        }

        return null === $type && \is_string($resource) && '/' === substr($resource, -1);
    }
}