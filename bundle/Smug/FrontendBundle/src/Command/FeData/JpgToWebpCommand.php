<?php

namespace Smug\FrontendBundle\Command\FeData;

use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\FrontendBundle\Service\Frontend\FeData\FeImagePrepareService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class JpgToWebpCommand extends Command
{
    private ContainerInterface $container;

    public function __construct(KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('jpg:to:wep')
            ->setDescription('transforms jpg images to webp.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('transforms jpg images to webp');

        ServiceGenerationFactory::init($this->container);

        /** @var FeImagePrepareService $service */
        $service = ServiceGenerationFactory::createInstance(FeImagePrepareService::class);

        $finder = new Finder();
        // find all files in the current directory
        $finder->files()->in(__DIR__ . '/../public/_uploads/images/media/fallback/thumbnails')
            ->name(['*.jpg']);

        $progressBar = new ProgressBar($output, count($finder));
        $progressBar->start();
        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'jpg')) {
                continue;
            }

            // $output->writeln('transforming  file ' . $file->getRealPath());

            $service->transform($file->getFilenameWithoutExtension(), $file->getRealPath());
            unlink($file->getRealPath());

            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('Complete: Images prepared for frontend usage');
	    
        return 0;
    }
}
