<?php

namespace Combyna\Component\Config\FileSystem;

use Symfony\Component\Config\Loader\FileLoader;

class DirectoryLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $collection = new ConfigCollection();

        foreach (scandir($path) as $dirName) {
            if ('.' !== $dirName[0]) {
                $this->setCurrentDir($path);
                $subPath = $path.'/'.$dirName;
                $subType = null;

                if (is_dir($subPath)) {
                    $subPath .= '/';
                    $subType = 'directory';
                }

                $subConfig = $this->import($subPath, $subType, false, $path);
                $collection->add($dirName, $subConfig);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'directory';
    }
}
