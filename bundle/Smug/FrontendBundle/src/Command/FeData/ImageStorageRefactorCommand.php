<?php

namespace Smug\FrontendBundle\Command\FeData;

use Smug\PublicationBundle\Entity\Publication\Publication;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Frontend\FeData\Prepare\PublicationPrepareService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageStorageRefactorCommand extends Command
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('image:storage:refactor')
            ->setDescription('refactor thumbnail folder.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('refactor thumbnail folder');

        ServiceGenerationFactory::init($this->container);
        $publicatoinIds = [];

        $output->writeln('Start: Rebuiding Publication JSONs ');
        $publicatoinIds= array_unique(DataHandler::getJsonDecode(DataHandler::getFile(__DIR__ . '/rebuildPublications.json'), true));



        $progressBar = new ProgressBar($output, count($publicatoinIds));

        foreach ($publicatoinIds as $publicatoinId) {
            /** Publication $publication */
            $publication = $this->em->getRepository(Publication::class)->findOneBy(['isbnLong' => $publicatoinId]);

            $arPublicationData = PublicationPrepareService::prepare($publication, [], $this->em);

            DataHandler::writeFile($arPublicationData['path'], DataHandler::getJsonEncode($arPublicationData['data']));
            try {

                $publicatoinIds = DataHandler::unsetArrayElement($publicatoinIds, $publicatoinId);
            } catch (\Exception $exception) {
                return false;
            }

            $progressBar->advance();
        }
        DataHandler::writeFile(__DIR__ . 'rebuildPublications.json', DataHandler::getJsonEncode($publicatoinIds));

        $progressBar->finish();

        $output->writeln('Complete: Rebuilding Publication JSONs');
	    
        return 0;
    }
}
