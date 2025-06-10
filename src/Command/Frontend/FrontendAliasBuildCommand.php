<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class FrontendAliasBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('frontend:alias:build')
            ->setDescription('collects and builds bundle aliases');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['frontendAliases.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Webpack Package Aliases');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'json')) {
                continue;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()))
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Webpack Package Aliases');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/webpack-aliases.json', DataHandler::getJsonEncode($assets));

        $output->writeln('Done');
        $output->writeln('#####################');
        $output->writeln('Complete: webpack aliases collected');
	    
        return 0;
    }
}
