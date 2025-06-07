<?php

namespace Smug\Core\Entity\Base;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Document
 * @package Smug\Core\Entity\Base
 */
class Document
{
    // TODO check functionality and fix mess
    private $file;

    private $subDir;

    private $filePersistencePath;

    /** @var string */
    protected static string $uploadDirectory = '%kernel.root_dir%/../uploads';

    /**
     * @param $dir
     */
    static public function setUploadDirectory($dir)
    {
        self::$uploadDirectory = $dir;
    }

    /**
     * @return string
     */
    static public function getUploadDirectory()
    {
        if (self::$uploadDirectory === null) {
            throw new \RuntimeException("Trying to access upload directory for profile files");
        }
        return self::$uploadDirectory;
    }

    /**
     * @param $dir
     */
    public function setSubDirectory($dir)
    {
        $this->subDir = $dir;
    }

    /**
     * @return mixed
     */
    public function getSubDirectory()
    {
        if ($this->subDir === null) {
            throw new \RuntimeException("Trying to access sub directory for profile files");
        }
        return $this->subDir;
    }


    public function setFile(File $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return new File(self::getUploadDirectory() . "/" . $this->filePersistencePath);
    }

    public function getOriginalFileName()
    {
        return $this->file->getClientOriginalName();
    }

    public function getFilePersistencePath()
    {
        return $this->filePersistencePath;
    }

    /**
     * @return bool
     */
    public function processFile(): bool
    {
        if (!($this->file instanceof UploadedFile)) {
            return false;
        }
        $uploadFileMover = new UploadFileMover();
        $this->filePersistencePath = $uploadFileMover->moveUploadedFile(
            $this->file,
            self::getUploadDirectory(),
            $this->subDir
        );

        return true;
    }
}