<?php

namespace Smug\Core\Service\Base\Components\Handler;

/**
 * Class FolderHandler
 * @package Smug\Core\Service\Base\Components\Handler
 */
class FolderHandler
{
    /**
     * @param string $folder
     * @return boolean
     */
    public static function removeFolder($folder)
    {
        $files = array_diff(scandir($folder), ['.', '..']);

        foreach ($files as $file) {
            (is_dir("$folder/$file")) ? self::removeFolder("$folder/$file") : unlink("$folder/$file");
        }

        return rmdir($folder);
    }
}
