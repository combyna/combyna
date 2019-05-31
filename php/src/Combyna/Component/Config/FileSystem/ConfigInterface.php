<?php
/**
 * Created by PhpStorm.
 * User: robincawser
 * Date: 2019-05-31
 * Time: 20:36
 */

namespace Combyna\Component\Config\FileSystem;

interface ConfigInterface
{
    /**
     * @return array
     */
    public function toArray();
}