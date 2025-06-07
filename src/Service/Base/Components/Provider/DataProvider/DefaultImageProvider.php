<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class DefaultImageProvider
{
    /** @var string $rootDir */
    protected string $rootDir;

    /** @var string $appDirectory */
    protected string $appDirectory;

    /** @var string $bundleDirectory */
    protected string $bundleDirectory;

    /** @var string $publicDirectory */
    protected string $publicDirectory;

    /**
     * DefaultImageProvider constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
        $this->appDirectory = $this->rootDir . '/custom/';
        $this->bundleDirectory = $this->rootDir . '/src/';
        $this->publicDirectory = $this->rootDir . '/public/';

    }

    const MISSING_IMAGE_FALLBACK = '/Resources/media/image/noimage.jpg';

    const APP_DIRECTORY = '/../../../../bundle/';

    const SYSTEM_BUNDLE_DIRECTORY = '/../../../../';

    const BASE_DIRECTORY = '/../../../../';

    /**
     * @param string $bundle
     * @param string $image
     * @param string $area
     * @return string
     */
    public static function getDefaultImage(string $bundle, string $image, string $area)
    {
        $finder = new Finder();

        $finder->files()->name('defaultImages.json');

        if ($area === 'apps') {
            $finder->in(__DIR__ . self::APP_DIRECTORY . $bundle);
        } else {
            $finder->in(__DIR__ . self::SYSTEM_BUNDLE_DIRECTORY . $bundle);
        }

        foreach ($finder as $file) {
            $images = DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()), true);

            if (!DataHandler::doesKeyExists($image, $images)) {
                return PathProvider::getHost() . '/' . self::MISSING_IMAGE_FALLBACK;
            }

            return PathProvider::getHost() . '/' . $images[$image];
        }

        return PathProvider::getHost() . '/' . self::MISSING_IMAGE_FALLBACK;
    }
}
