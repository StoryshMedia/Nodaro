<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class FrontendStyleBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('frontend:styles:build')
            ->setDescription('collects all style files.');
    }

    protected function execute(OutputInterface $output): int
    {
        $output->writeln('Searching style files');
        $output->writeln('#####################');

        $assets = [
        ];

        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['*.scss']);

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'scss')) {
                continue;
            }
            if (!DataHandler::isStringInString($file->getRelativePath(), 'styles')) {
                continue;
            }

            $assets[] = [
                'path' => $this->kernel->getProjectDir() . "/bundle/" . $file->getRelativePath() . '/' . $file->getFileName(),
                'style' => self::getStylePrefix($file->getRelativePath()) . '-' . DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-')
            ];
        }
        
        $output->writeln('Done');
        $output->writeln('Writing styles File');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/webpack-styles.json', DataHandler::getJsonEncode($assets));
        
        $output->writeln('Done');
        $output->writeln('Complete: Writing frontend style sheets');
        $output->writeln('#####################');
	    
        return 0;
    }

    private static function getStylePrefix(string $path): string
    {
        $pathArray = DataHandler::explodeArray('/', $path);
        
        return DataHandler::getLowerString($pathArray[0]) . '-' . DataHandler::getLowerString($pathArray[1]);
    }
}
