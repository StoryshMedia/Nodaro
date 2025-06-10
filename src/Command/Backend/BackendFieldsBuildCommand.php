<?php

namespace Smug\Core\Command\Backend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class BackendFieldsBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('backend:fields:build')
            ->setDescription('collects all backend administration fields');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['fields.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Administration Assets');
        $output->writeln('#####################');

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'json')) {
                continue;
            }

            $components = [];

            foreach (DataHandler::getJsonDecode(DataHandler::getFile($file->getRealPath()), true) as $component) {
                $bundle = '';
                $nameSpace = '';
                
                foreach (DataHandler::explodeArray('/', $file->getPath()) as $partKey => $part) {
                    if (DataHandler::isStringInString($part, 'Bundle') && $part !== 'bundle') {
                        $bundle = $part;
                        $nameSpace = DataHandler::explodeArray('/', $file->getPath())[$partKey - 1];
                        break;
                    }
                }

                $componentFinder = new Finder();
                $componentFinder->files()->in($this->kernel->getProjectDir() . "/bundle/" . $nameSpace . '/' . $bundle)->name([$component['name'] . '.vue']);
                $componentFilePath = '';
                
                foreach ($componentFinder as $componentFile) {
                    $componentFilePath = $componentFile->getRelativePath();
                }

                $component['path'] = $nameSpace . '/' . $bundle . '/' . $componentFilePath . '/' . $component['name'];

                $components[] = $component;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                $components
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Administration Assets');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeFields.json', DataHandler::getJsonEncode($assets));

        $output->writeln('Done');
        $output->writeln('#####################');
        $output->writeln('Complete: Threads were generated');
	    
        return 0;
    }
}
