<?php

namespace Smug\FrontendBundle\Command\FeData;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\FrontendBundle\Service\Frontend\FeData\FeImagePrepareService;
use Doctrine\ORM\EntityManagerInterface;
use Smug\Core\Context\Context;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageOptimizeCommand extends Command
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private Context $context;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel, Context $context)
    {
        $this->container = $kernel->getContainer();
        $this->em = $em;
        $this->context = $context;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('image:fe:optimization')
            ->setDescription('optimizing images for frontend and generates thumbnails.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('optimizing images for frontend and generates thumbnails');

        ServiceGenerationFactory::init($this->container);

        $service = ServiceGenerationFactory::createInstance(FeImagePrepareService::class);

        $images = $this->em->getRepository(Media::class)->findBy(['optimized' => false]);

        $progressBar = new ProgressBar($output, count($images));

        $progressBar->start();

        /** @var Media $image */
        foreach ($images as $image) {
            $service->image($image, $this->context);
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('Complete: Images prepared for frontend usage');
	    
        return 0;
    }
}
