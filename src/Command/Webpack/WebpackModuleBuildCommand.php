<?php

namespace Smug\Core\Command\Webpack;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class WebpackModuleBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('webpack:module:build')
            ->setDescription('collects all asset files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Searching asset files');
        $output->writeln('#####################');

        $assets = [
        ];

        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['*.js']);

        $output->writeln('Done');
        $output->writeln('Collecting assets');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'js')) {
                continue;
            }
            if (!DataHandler::isStringInString($file->getRelativePath(), 'modules')) {
                continue;
            }[]

            $assets[] = [
                'path' => $this->kernel->getProjectDir() . "/bundle/" . $file->getRelativePath() . '/' . $file->getFileName(),
                'module' => DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-')
            ];
        }
        
        $output->writeln('Done');
        $output->writeln('Writing Config File');
        $output->writeln('#####################');

        DataHandler::writeFile(__DIR__ . '/webpack-modules.json', DataHandler::getJsonEncode($assets));

        $output->writeln('Done');
        $output->writeln('Complete: Module File written');
        $output->writeln('#####################');
	    
        return 0;
    }
}
