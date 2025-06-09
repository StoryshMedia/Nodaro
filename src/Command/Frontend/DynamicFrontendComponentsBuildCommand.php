<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class DynamicFrontendComponentsBuildCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('dynamic:frontend:component:build')
            ->setDescription('collects all dynamic frontend components');
    }

    protected function execute(OutputInterface $output): int
    {
        $assets = [];
        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . "/bundle")->name(['dynamicFrontendComponents.json']);

        $output->writeln('Done');
        $output->writeln('Collecting Dynamic Frontend Components');
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

                if (DataHandler::isEmpty($component['path'] ?? '')) {
                    $component['path'] = $nameSpace . '/' . $bundle . '/' . $componentFilePath . '/' . $component['name'];
                }

                $components[] = $component;
            }

            $assets = DataHandler::mergeArray(
                $assets,
                $components
            );
        }

        $output->writeln('Done');
        $output->writeln('Writing Dynamic Frontend Components');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeComponents.json', DataHandler::getJsonEncode($assets));
        
        $output->writeln('Done');
        $output->writeln('#####################');
        $output->writeln('Complete: Dynamic frontendcomponents collected');
	    
        return 0;
    }
}
