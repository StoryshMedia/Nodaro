<?php

namespace Smug\FrontendBundle\Command\FeData;

use Smug\Core\Entity\System\Media\MediaThumbnail;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MissingRefactorImages extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('missing:jpg:images')
            ->setDescription('adjust database jpg thumbnail images.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('transforms jpg images to webp');

        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../Base/data/thumbnails/');

        $progressBar = new ProgressBar($output, count($finder));

        $progressBar->start();

        $thumbnailRepository = $this->em->getRepository(MediaThumbnail::class);

        foreach ($finder as $path) {

            $newPath = DataHandler::getJsonDecode(
                DataHandler::getFile($path->getRealPath())
            );

            $newPath = DataHandler::getReplaceString('../public/', '', $newPath);
            $lastBackSlash = strrpos($newPath, '/');
            $fileName = $path->getFilename();
            $fileName = DataHandler::getReplaceString('.json', '', $fileName);
            $fileNameParts = DataHandler::explodeArray('_', $fileName);
            $newPath = substr($newPath, 0, $lastBackSlash);
            
            $thumbnail = $thumbnailRepository->findOneBy([
                'file' => $fileNameParts[0] . '_' . $fileNameParts[1],
                'viewport' => $fileNameParts[2]
            ]);

            if (!DataHandler::isEmpty($thumbnail)) {
                $thumbnail->setPath($path);
                $thumbnail->setExtension('webp');
    
                $this->em->persist($thumbnail);
                $this->em->flush();
            }

            DataHandler::deleteFile($path->getRealPath());
            
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('Complete: Images prepared for frontend usage');
	    
        return 0;
    }
}
