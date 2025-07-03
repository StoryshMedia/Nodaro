<?php

namespace Smug\Core\Command\Frontend;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\Finder\FinderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class FrontendFieldsBuildCommand extends Command
{
    private KernelInterface $kernel;
    protected Context $context;

    public function __construct(KernelInterface $kernel, Context $context)
    {
        $this->context = $context;
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('frontend:form:fields:build')
            ->setDescription('collects all available frontend form fields');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Searching style files');
        $output->writeln('#####################');

        $assets = [
        ];
        
        $output->writeln('Done');
        $output->writeln('Collecting Frontend Formular Fields');
        $output->writeln('#####################');

        foreach (
            DataHandler::mergeArray(
                FinderFactory::getElements($this->context->getKernel()->getProjectDir() . "/bundle/", 0, false, [], true),
                FinderFactory::getElements($this->context->getKernel()->getProjectDir() . "/custom/", 0, false, [], true)
            ) as $namespace) {
            $bundleFinder = new Finder();
            $bundleFinder->directories()->in($namespace->getPathname())->depth(0);

            foreach ($bundleFinder as $bundle) {
                $output->writeln('Searching frontend fields in: ' . $namespace->getBasename() . ' => ' . $bundle->getBasename());
                
                if (DataHandler::proofDir($bundle->getPathname() . '/config')) {
                    $fieldFinder = new Finder();
                    $fieldFinder
                        ->files()
                        ->in($bundle->getPathname() . '/config')
                        ->name(['frontendFields.json']);

                    foreach ($fieldFinder as $fieldFile) {
                        $components = [];

                        foreach (DataHandler::getJsonDecode(DataHandler::getFile($fieldFile->getRealPath()), true) as $component) {
                            $bundleString = '';
                            $nameSpace = '';
                            $type = 'custom';
                            
                            foreach (DataHandler::explodeArray('/', $fieldFile->getPath()) as $partKey => $part) {
                                if ($part === 'bundle') {
                                    $type = 'bundle';
                                }

                                if (DataHandler::isStringInString($part, 'Bundle') && $part !== 'bundle') {
                                    $bundleString = $part;
                                    $nameSpace = DataHandler::explodeArray('/', $fieldFile->getPath())[$partKey - 1];
                                    break;
                                }
                            }

                            $componentFinder = new Finder();
                            $componentFinder->files()->in($bundle->getPathname())->name([$component['name'] . '.vue']);
                            $componentFilePath = '';
                            
                            foreach ($componentFinder as $componentFile) {
                                $componentFilePath = $componentFile->getRelativePath();
                            }

                            $component['path'] = $type . '/' . $nameSpace . '/' . $bundleString . '/' . $componentFilePath . '/' . $component['name'];

                            $components[] = $component;
                        }
                        $assets = DataHandler::mergeArray(
                            $assets,
                            $components
                        );
                    }
                }
            }
        }

        $output->writeln('Done');
        $output->writeln('Writing Frontend Formular Fields');
        $output->writeln('#####################');

        DataHandler::writeFile($this->kernel->getProjectDir() . '/bundle/activeFormFields.json', DataHandler::getJsonEncode($assets));

        $output->writeln('Done');
        $output->writeln('#####################');
        $output->writeln('Complete: All available frontend form fields collected');
	    
        return 0;
    }
}
